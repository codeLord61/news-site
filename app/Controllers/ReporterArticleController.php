<?php

namespace app\controllers;

use app\core\App;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\middleware\AuthMiddleware;
use app\models\Article;
use app\models\Category;
use app\models\Media;
use app\models\Tag;
use app\models\Token;
use app\models\User;

class ReporterArticleController extends Controller
{
    private const MAX_IMAGE_UPLOAD_BYTES = 5242880; // 5MB

    /** @var array<string,string> */
    private const ALLOWED_IMAGE_MIME_EXT = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    /** @var string[] */
    private const ALLOWED_URL_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /** @var string[] */
    private const ALLOWED_STYLE_PROPERTIES = [
        'color',
        'background-color',
        'font-size',
        'font-family',
        'text-align',
    ];

    private Article $article;
    private Category $category;
    private Media $media;
    private Tag $tag;
    private Token $token;
    private User $user;

    /**
     * Initialize reporter article dependencies and enforce API auth middleware.
     */
    public function __construct()
    {
        $this->article = new Article();
        $this->category = new Category();
        $this->media = new Media();
        $this->tag = new Tag();
        $this->token = new Token();
        $this->user = new User();

        $this->registerMiddleware(new AuthMiddleware());
    }

    /**
     * POST /api/v1/reporter/articles
     * Create or update an article owned by the authenticated reporter.
     */
    public function save(Request $request, Response $response): void
    {
        $reporter = $this->resolveReporter($request, $response);
        $body = $request->getBody();

        $errors = [];

        // Parse optional numeric IDs from payload ("12" -> 12, invalid -> null + error).
        $articleId = $this->parseOptionalPositiveInt($body, 'article_id', $errors);
        $title = trim((string)($body['title'] ?? ''));
        $excerpt = trim((string)($body['excerpt'] ?? ''));
        $intent = strtolower(trim((string)($body['intent'] ?? '')));
        $categoryId = $this->parseOptionalPositiveInt($body, 'category_id', $errors);
        $tagId = $this->parseOptionalPositiveInt($body, 'tag_id', $errors);

        $mediaIdsProvided = array_key_exists('media_ids', $body);
        $mediaIds = [];
        if ($mediaIdsProvided) {
            $mediaIds = $this->parseMediaIds($body['media_ids'], $errors);
        }

        $thumbnailMediaId = $this->parseOptionalPositiveInt($body, 'thumbnail_media_id', $errors);
        if ($thumbnailMediaId !== null) {
            // Ensure thumbnail media is always included in linked media list.
            $mediaIds[] = $thumbnailMediaId;
            $mediaIdsProvided = true;
        }

        if ($title === '') {
            $errors['title'] = 'Title is required.';
        } elseif ($this->strLength($title) > 255) {
            $errors['title'] = 'Title may not be greater than 255 characters.';
        }

        if ($excerpt !== '' && $this->strLength($excerpt) > 255) {
            $errors['excerpt'] = 'Excerpt may not be greater than 255 characters.';
        }

        if (!in_array($intent, ['draft', 'submit'], true)) {
            $errors['intent'] = 'Intent must be either draft or submit.';
        }

        if ($categoryId !== null && !$this->category->findById($categoryId)) {
            $errors['category_id'] = 'Selected category is invalid.';
        }

        if ($tagId !== null && !$this->tag->findById($tagId)) {
            $errors['tag_id'] = 'Selected tag is invalid.';
        }

        $reporterId = (int)$reporter['id'];
        if ($mediaIdsProvided && !empty($mediaIds)) {
            $ownedIds = $this->media->findOwnedIds($mediaIds, $reporterId);
            sort($ownedIds);
            $submittedIds = $mediaIds;
            sort($submittedIds);

            if (count($ownedIds) !== count($submittedIds) || $ownedIds !== $submittedIds) {
                $errors['media_ids'] = 'One or more media items are invalid or not owned by this reporter.';
            }
        }


        if (!empty($errors)) {
            $response->json([
                'error' => 'Validation failed.',
                'fields' => $errors,
            ], 422);
        }

        // Convert UI intent into stored article status.
        // Example: "submit" -> "submitted", "draft" -> "draft".
        $status = $intent === 'submit' ? 'submitted' : 'draft';
        $rawContentHtml = (string)($body['content_html'] ?? ($body['content'] ?? ''));
        // Sanitize rich HTML before persisting to prevent unsafe markup/scripts.
        $safeContentHtml = $this->sanitizeContentHtml($rawContentHtml);
        if ($safeContentHtml === '' && trim(strip_tags($rawContentHtml)) !== '') {
            $plain = htmlspecialchars(
                trim(strip_tags($rawContentHtml)),
                ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5,
                'UTF-8'
            );
            $safeContentHtml = '<p>' . nl2br($plain, false) . '</p>';
        }
        // Store empty excerpt as NULL (database-friendly semantic "no excerpt").
        $normalizedExcerpt = $excerpt === '' ? null : $excerpt;

        $payload = [
            'title' => $title,
            'excerpt' => $normalizedExcerpt,
            'content_html' => $safeContentHtml,
            'status' => $status,
            'category_id' => $categoryId,
            'tag_id' => $tagId,
        ];

        if ($mediaIdsProvided) {
            $payload['media_ids'] = $mediaIds;
        }

        $savedArticleId = $articleId;
        $slug = '';

        if ($articleId !== null) {
            $existing = $this->article->findByIdForReporter($articleId, $reporterId);
            if (!$existing) {
                $response->json(['error' => 'Article not found for this reporter.'], 404);
            }

            $slug = $existing['slug'];
            if ($existing['title'] !== $title) {
                // Regenerate slug only when title changed.
                $slug = $this->article->generateUniqueSlug($title, $articleId);
            }

            $payload['slug'] = $slug;
            $this->article->updateReporterArticle($articleId, $reporterId, $payload);
        } else {
            $slug = $this->article->generateUniqueSlug($title);
            $payload['slug'] = $slug;
            $savedArticleId = $this->article->createReporterArticle($reporterId, $payload);
        }

        $saved = $this->article->findByIdForReporter((int)$savedArticleId, $reporterId);
        $isUpdate = $articleId !== null;
        $message = $intent === 'submit'
            ? ($isUpdate ? 'Article updated and submitted for review.' : 'Article saved and submitted for review.')
            : ($isUpdate ? 'Article updated successfully.' : 'Draft saved successfully.');

        $response->json([
            'success' => true,
            'data' => [
                'id' => (int)$savedArticleId,
                'slug' => $saved['slug'] ?? $slug,
                'status' => $saved['status'] ?? $status,
                'updated_at' => $saved['updated_at'] ?? date('Y-m-d H:i:s'),
            ],
            'message' => $message,
        ]);
    }

    /**
     * POST /api/v1/reporter/articles/delete
     * Soft-delete a reporter-owned article.
     */
    public function delete(Request $request, Response $response): void
    {
        if ($request->getMethod() !== 'post') {
            $response->json(['error' => 'Method not allowed.'], 405);
        }

        $reporter = $this->resolveReporter($request, $response);
        $body = $request->getBody();
        $errors = [];

        $articleId = $this->parseOptionalPositiveInt($body, 'article_id', $errors);
        if ($articleId === null || !empty($errors)) {
            $response->json(['error' => 'A valid article_id is required.'], 422);
        }

        $deleted = $this->article->deleteReporterArticle($articleId, (int)$reporter['id']);
        if (!$deleted) {
            $response->json(['error' => 'Article not found for this reporter.'], 404);
        }

        $response->json([
            'success' => true,
            'message' => 'Article deleted successfully.',
        ]);
    }

    /**
     * POST /api/v1/reporter/media/images
     * Upload an image (file or URL mode) and create media metadata.
     */
    public function uploadImage(Request $request, Response $response): void
    {
        $reporter = $this->resolveReporter($request, $response);

        $body = $request->getBody();
        $errors = [];

        $altText = trim((string)($body['alt_text'] ?? ($_POST['alt_text'] ?? '')));
        $title = trim((string)($body['title'] ?? ($_POST['title'] ?? '')));

        if ($altText !== '' && $this->strLength($altText) > 255) {
            $errors['alt_text'] = 'Alt text may not be greater than 255 characters.';
        }

        if ($title !== '' && $this->strLength($title) > 255) {
            $errors['title'] = 'Title may not be greater than 255 characters.';
        }

        $fileUrl = '';

        $hasUpload = isset($_FILES['image']) && is_array($_FILES['image'])
            && (int)($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

        if ($hasUpload) {
            $fileMeta = $_FILES['image'];
            $extension = $this->validateUploadedImage($fileMeta, $errors);

            if (empty($errors)) {
                $uploadDir = App::$ROOT_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets'
                    . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'articles';

                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                    $response->json(['error' => 'Unable to prepare upload directory.'], 500);
                }

                try {
                    $fileName = date('YmdHis') . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
                } catch (\Throwable $e) {
                    $fileName = date('YmdHis') . '_' . uniqid('', true) . '.' . $extension;
                }

                $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
                if (!move_uploaded_file((string)$fileMeta['tmp_name'], $targetPath)) {
                    $response->json(['error' => 'Failed to store uploaded image.'], 500);
                }

                // Public URL stored in DB and used by frontend editor.
                $fileUrl = url('/assets/uploads/articles/' . $fileName);
            }
        } else {
            $imageUrl = trim((string)($body['image_url'] ?? ($_POST['image_url'] ?? '')));
            if ($imageUrl === '') {
                $errors['image'] = 'Provide an image file or image URL.';
            } else {
                $this->validateImageUrl($imageUrl, $errors);
                $fileUrl = $imageUrl;
            }
        }

        if (!empty($errors)) {
            $response->json([
                'error' => 'Validation failed.',
                'fields' => $errors,
            ], 422);
        }

        $isThumbnail = filter_var(
            $body['is_thumbnail'] ?? ($_POST['is_thumbnail'] ?? false),
            FILTER_VALIDATE_BOOLEAN
        );

        // Create media row and return normalized media payload.
        $mediaId = $this->media->createImage(
            $fileUrl,
            $altText !== '' ? $altText : null,
            $title !== '' ? $title : null,
            (int)$reporter['id'],
            $isThumbnail
        );

        $response->json([
            'success' => true,
            'data' => [
                'media_id' => $mediaId,
                'file_url' => $fileUrl,
                'alt_text' => $altText,
                'title' => $title,
            ],
            'message' => 'Image uploaded successfully.',
        ]);
    }

    /**
     * Resolve authenticated reporter user from token.
     *
     * Input: cookie/bearer token.
     * Output: user row for reporter role.
     * On failure: returns 401/403 JSON and exits.
     */
    private function resolveReporter(Request $request, Response $response): array
    {
        $tokenStr = $_COOKIE['auth_token'] ?? $request->getBearerToken();
        if (!$tokenStr) {
            $response->json(['error' => 'Unauthorized'], 401);
        }

        $tokenData = $this->token->findValid((string)$tokenStr);
        if (!$tokenData) {
            $response->json(['error' => 'Unauthorized'], 401);
        }

        $user = $this->user->findById((int)$tokenData['user_id']);
        if (!$user || strtolower((string)($user['role_name'] ?? '')) !== 'reporter') {
            $response->json(['error' => 'Forbidden: Reporter role required.'], 403);
        }

        return $user;
    }

    /**
     * Parse optional positive integer field from payload.
     *
     * Input: request body array + field name.
     * Output: positive int or null. Adds validation error when invalid.
     */
    private function parseOptionalPositiveInt(array $body, string $field, array &$errors): ?int
    {
        $rawValue = $body[$field] ?? null;
        if ($rawValue === null || $rawValue === '') {
            return null;
        }

        $validated = filter_var($rawValue, FILTER_VALIDATE_INT);
        if ($validated === false || (int)$validated <= 0) {
            $errors[$field] = 'Invalid ' . str_replace('_', ' ', $field) . '.';
            return null;
        }

        return (int)$validated;
    }

    /**
     * @return int[]
     */
    private function parseMediaIds(mixed $rawMediaIds, array &$errors): array
    {
        if (!is_array($rawMediaIds)) {
            $errors['media_ids'] = 'media_ids must be an array of positive integers.';
            return [];
        }

        $parsed = [];
        foreach ($rawMediaIds as $rawMediaId) {
            $validated = filter_var($rawMediaId, FILTER_VALIDATE_INT);
            if ($validated === false || (int)$validated <= 0) {
                $errors['media_ids'] = 'media_ids must contain only positive integers.';
                return [];
            }
            $parsed[] = (int)$validated;
        }

        $parsed = array_values(array_unique($parsed));
        return $parsed;
    }

    /**
     * @param array<string,mixed> $fileMeta
     */
    private function validateUploadedImage(array $fileMeta, array &$errors): string
    {
        $uploadError = (int)($fileMeta['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($uploadError !== UPLOAD_ERR_OK) {
            $errors['image'] = match ($uploadError) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Image exceeds maximum upload size.',
                UPLOAD_ERR_PARTIAL => 'Image upload was interrupted. Try again.',
                UPLOAD_ERR_NO_FILE => 'Image file is required.',
                default => 'Failed to upload image.',
            };
            return '';
        }

        $tmpName = (string)($fileMeta['tmp_name'] ?? '');
        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            $errors['image'] = 'Invalid image upload payload.';
            return '';
        }

        $fileSize = (int)($fileMeta['size'] ?? 0);
        if ($fileSize <= 0) {
            $errors['image'] = 'Uploaded image is empty.';
            return '';
        }

        if ($fileSize > self::MAX_IMAGE_UPLOAD_BYTES) {
            $errors['image'] = 'Image size must be 5MB or less.';
            return '';
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = strtolower((string)$finfo->file($tmpName));

        if (!array_key_exists($mimeType, self::ALLOWED_IMAGE_MIME_EXT)) {
            $errors['image'] = 'Unsupported image type. Allowed: JPG, JPEG, PNG, WEBP, GIF.';
            return '';
        }

        return self::ALLOWED_IMAGE_MIME_EXT[$mimeType];
    }

    /**
     * Validate remote image URL input from editor.
     *
     * Input: URL string.
     * Output: none. Adds field errors when URL/suffix is invalid.
     */
    private function validateImageUrl(string $imageUrl, array &$errors): void
    {
        if (!$this->isSafeUrl($imageUrl)) {
            $errors['image_url'] = 'Image URL must be a valid http/https URL.';
            return;
        }

        $path = (string)parse_url($imageUrl, PHP_URL_PATH);
        $extension = strtolower((string)pathinfo($path, PATHINFO_EXTENSION));
        if ($extension === '' || !in_array($extension, self::ALLOWED_URL_EXTENSIONS, true)) {
            $errors['image_url'] = 'Image URL must end with jpg, jpeg, png, webp, or gif.';
        }
    }

    /**
     * Multi-byte safe length helper (falls back to strlen).
     *
     * Input: UTF-8 string.
     * Output: character count.
     */
    private function strLength(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }

    /**
     * Sanitizes saved rich text HTML.
     */
    private function sanitizeContentHtml(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $wrapped = '<!DOCTYPE html><html><body><div id="__root__">' . $html . '</div></body></html>';
        $htmlToLoad = function_exists('mb_convert_encoding')
            ? mb_convert_encoding($wrapped, 'HTML-ENTITIES', 'UTF-8')
            : $wrapped;

        $useInternalErrors = libxml_use_internal_errors(true);
        $dom->loadHTML($htmlToLoad);
        libxml_clear_errors();
        libxml_use_internal_errors($useInternalErrors);

        $rootNodes = $dom->getElementsByTagName('div');
        $root = null;
        foreach ($rootNodes as $candidate) {
            if ($candidate->getAttribute('id') === '__root__') {
                $root = $candidate;
                break;
            }
        }

        if (!$root) {
            return '';
        }

        $allowedTags = [
            'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'ul', 'ol', 'li',
            'blockquote', 'code', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a',
            'img', 'span', 'hr', 'mark',
        ];

        $allowedAttributesByTag = [
            'a' => ['href', 'title', 'target', 'rel'],
            'img' => ['src', 'alt', 'title', 'data-media-id'],
            'p' => ['style'],
            'h1' => ['style'],
            'h2' => ['style'],
            'h3' => ['style'],
            'h4' => ['style'],
            'h5' => ['style'],
            'h6' => ['style'],
            'mark' => ['style', 'data-color'],
            'span' => ['style'],
        ];

        for ($child = $root->firstChild; $child !== null;) {
            $next = $child->nextSibling;
            // Recursively remove unsafe tags/attributes in-place.
            $this->sanitizeNodeRecursive($child, $allowedTags, $allowedAttributesByTag);
            $child = $next;
        }

        $sanitizedHtml = '';
        foreach ($root->childNodes as $childNode) {
            $sanitizedHtml .= $dom->saveHTML($childNode);
        }

        return trim($sanitizedHtml);
    }

    /**
     * @param array<string,string[]> $allowedAttributesByTag
     */
    private function sanitizeNodeRecursive(
        \DOMNode $node,
        array $allowedTags,
        array $allowedAttributesByTag
    ): void {
        if ($node->nodeType === XML_ELEMENT_NODE) {
            /** @var \DOMElement $element */
            $element = $node;
            $tag = strtolower($element->tagName);

            if (!in_array($tag, $allowedTags, true)) {
                // Unknown tags are unwrapped (children are kept, wrapper is removed).
                for ($child = $element->firstChild; $child !== null;) {
                    $nextChild = $child->nextSibling;
                    $this->sanitizeNodeRecursive($child, $allowedTags, $allowedAttributesByTag);
                    $child = $nextChild;
                }

                $parent = $element->parentNode;
                if ($parent) {
                    while ($element->firstChild) {
                        $parent->insertBefore($element->firstChild, $element);
                    }
                    $parent->removeChild($element);
                }
                return;
            }

            $allowedAttributes = $allowedAttributesByTag[$tag] ?? [];
            for ($i = $element->attributes->length - 1; $i >= 0; $i--) {
                $attribute = $element->attributes->item($i);
                if (!$attribute) {
                    continue;
                }

                $attributeName = strtolower($attribute->name);
                $attributeValue = trim($attribute->value);

                if (str_starts_with($attributeName, 'on') || !in_array($attributeName, $allowedAttributes, true)) {
                    $element->removeAttributeNode($attribute);
                    continue;
                }

                if (in_array($attributeName, ['href', 'src'], true) && !$this->isSafeUrl($attributeValue)) {
                    $element->removeAttributeNode($attribute);
                    continue;
                }

                if ($attributeName === 'data-media-id' && (!ctype_digit($attributeValue) || (int)$attributeValue <= 0)) {
                    $element->removeAttributeNode($attribute);
                    continue;
                }

                if ($attributeName === 'style') {
                    // Keep only approved inline style declarations.
                    $safeStyle = $this->sanitizeInlineStyle($attributeValue);
                    if ($safeStyle === '') {
                        $element->removeAttributeNode($attribute);
                    } else {
                        $element->setAttribute('style', $safeStyle);
                    }
                    continue;
                }

                if ($attributeName === 'target') {
                    if (!in_array($attributeValue, ['_self', '_blank', '_parent', '_top'], true)) {
                        $element->setAttribute('target', '_self');
                    }
                    continue;
                }
            }

            if ($tag === 'a' && $element->getAttribute('target') === '_blank') {
                // Enforce safe external-link behavior.
                $element->setAttribute('rel', 'noopener noreferrer');
            }
        }

        for ($child = $node->firstChild; $child !== null;) {
            $next = $child->nextSibling;
            $this->sanitizeNodeRecursive($child, $allowedTags, $allowedAttributesByTag);
            $child = $next;
        }
    }

    /**
     * Strip unsafe CSS declarations from inline style text.
     *
     * Input example: "color:red; position:absolute".
     * Output example: "color: red" (unsafe properties dropped).
     */
    private function sanitizeInlineStyle(string $style): string
    {
        $safeDeclarations = [];
        $declarations = explode(';', $style);

        foreach ($declarations as $declaration) {
            $parts = explode(':', $declaration, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $property = strtolower(trim($parts[0]));
            $value = trim($parts[1]);

            if (!in_array($property, self::ALLOWED_STYLE_PROPERTIES, true)) {
                continue;
            }

            if ($value === '') {
                continue;
            }

            if (preg_match('/(?:expression\s*\(|javascript:|url\s*\(|@import|behavior\s*:)/i', $value)) {
                continue;
            }

            if (!preg_match('/^[#(),.%\w\s"\'-]+$/', $value)) {
                continue;
            }

            $safeDeclarations[] = $property . ': ' . $value;
        }

        return implode('; ', $safeDeclarations);
    }

    /**
     * Validate URL safety for editor content links/images.
     *
     * Allowed: relative URLs, http, https.
     * Blocked: javascript:, data:, vbscript:, malformed URLs.
     */
    private function isSafeUrl(string $url): bool
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($url === '') {
            return false;
        }

        if (preg_match('/^(javascript|data|vbscript):/i', $url)) {
            return false;
        }

        if (str_starts_with($url, '/')) {
            return true;
        }

        if (preg_match('/^\.{1,2}\//', $url)) {
            return true;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
        return in_array($scheme, ['http', 'https'], true);
    }
}
