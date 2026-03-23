<?php
use app\core\App;

$categories = $categories ?? [];
$tags = $tags ?? [];
?>

<div class="mb-5 flex flex-wrap items-center justify-end gap-2">
    <button type="button" id="cancelArticleBtn" class="text-white bg-danger box-border border border-transparent hover:bg-danger-strong focus:ring-4 focus:ring-danger-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Cancel</button>
    <button type="button" id="saveDraftBtn" data-intent="draft" class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Save as Draft</button>
    <button type="button" id="savePublishBtn" data-intent="submit" class="text-white bg-blue-600 box-border border border-transparent hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Save &amp; Publish</button>
</div>

<div id="articleFormAlert" class="hidden mb-4 rounded-base border px-4 py-3 text-sm" role="alert"></div>

<form id="reporter-article-form" class="space-y-5">
    <input type="hidden" id="articleId" name="article_id" value="">
    <input type="hidden" id="thumbnailMediaId" name="thumbnail_media_id" value="">
    <textarea id="contentHtml" name="content_html" class="hidden"></textarea>

    <section class="bg-neutral-primary-soft border border-default rounded-base shadow-xs">
        <div class="px-5 py-4 border-b border-default">
            <h2 class="text-lg font-semibold text-heading">Metadata</h2>
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-5">
                <div>
                    <div class="flex items-center justify-between gap-3">
                        <label for="titleInput" class="block mb-2.5 text-sm font-medium text-heading">Title <span class="text-red-600">*</span></label>
                        <span id="titleCounter" class="text-xs text-body">0/255</span>
                    </div>
                    <input type="text" id="titleInput" name="title" maxlength="255" required class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="Write your article title" />
                    <p id="titleError" class="mt-2 text-sm text-red-600 hidden"></p>
                </div>

                <div>
                    <div class="flex items-center justify-between gap-3">
                        <label for="excerptInput" class="block mb-2.5 text-sm font-medium text-heading">Excerpt</label>
                        <span id="excerptCounter" class="text-xs text-body">0/255</span>
                    </div>
                    <textarea id="excerptInput" name="excerpt" rows="4" maxlength="255" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-3 shadow-xs placeholder:text-body" placeholder="Optional short summary"></textarea>
                    <p id="excerptError" class="mt-2 text-sm text-red-600 hidden"></p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <label for="categorySelect" class="block mb-2.5 text-sm font-medium text-heading">Category</label>
                    <select id="categorySelect" name="category_id" class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body">
                        <option value="">Select category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int)$category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p id="categoryError" class="mt-2 text-sm text-red-600 hidden"></p>
                </div>

                <div>
                    <label for="tagSelect" class="block mb-2.5 text-sm font-medium text-heading">Tags</label>
                    <select id="tagSelect" name="tag_id" class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body">
                        <option value="">Select tag</option>
                        <?php foreach ($tags as $tag): ?>
                            <option value="<?= (int)$tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p id="tagError" class="mt-2 text-sm text-red-600 hidden"></p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-neutral-primary-soft border border-default rounded-base shadow-xs">
        <div class="px-5 py-4 border-b border-default">
            <h2 class="text-lg font-semibold text-heading">Thumbnail</h2>
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-5">
                <div>
                    <label for="thumbnailFileInput" class="block mb-2.5 text-sm font-medium text-heading">Upload thumbnail image</label>
                    <input type="file" id="thumbnailFileInput" name="thumbnail_image" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif" class="block w-full text-sm text-body file:mr-4 file:py-2 file:px-4 file:rounded-base file:border-0 file:text-sm file:font-medium file:bg-neutral-tertiary-medium file:text-heading hover:file:bg-neutral-quaternary" />
                    <p class="mt-1 text-xs text-body">Optional. Allowed: JPG, JPEG, PNG, WEBP, GIF. Max 5MB.</p>
                    <p id="thumbnailFileError" class="mt-2 text-sm text-red-600 hidden"></p>
                </div>

                <div>
                    <label for="thumbnailUrlInput" class="block mb-2.5 text-sm font-medium text-heading">Thumbnail image URL</label>
                    <input type="url" id="thumbnailUrlInput" name="thumbnail_image_url" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="https://placehold.co/1200x675" />
                    <p class="mt-1 text-xs text-body">Optional. If file upload is selected, uploaded file is used.</p>
                    <p id="thumbnailUrlError" class="mt-2 text-sm text-red-600 hidden"></p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <div class="flex items-center justify-between gap-3">
                        <label for="thumbnailAltInput" class="block mb-2.5 text-sm font-medium text-heading">Thumbnail alt text</label>
                        <span id="thumbnailAltCounter" class="text-xs text-body">0/255</span>
                    </div>
                    <input type="text" id="thumbnailAltInput" name="thumbnail_alt_text" maxlength="255" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="Describe the thumbnail image" />
                    <p id="thumbnailAltError" class="mt-2 text-sm text-red-600 hidden"></p>
                </div>

                <div>
                    <div class="flex items-center justify-between gap-3">
                        <label for="thumbnailCaptionInput" class="block mb-2.5 text-sm font-medium text-heading">Thumbnail caption</label>
                        <span id="thumbnailCaptionCounter" class="text-xs text-body">0/255</span>
                    </div>
                    <input type="text" id="thumbnailCaptionInput" name="thumbnail_caption" maxlength="255" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="Optional caption shown under featured image" />
                    <p id="thumbnailCaptionError" class="mt-2 text-sm text-red-600 hidden"></p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-neutral-primary-soft border border-default rounded-base shadow-xs">
        <div class="px-5 py-4 border-b border-default">
            <h2 class="text-lg font-semibold text-heading">Content</h2>
        </div>

        <div class="p-5">
            <div class="w-full bg-neutral-secondary-medium border border-default-medium rounded-base shadow-xs">
                <div class="p-2 border-b border-default-medium space-y-2">
                    <div class="flex flex-wrap items-center gap-1">
                        <button id="toggleBoldButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Bold</button>
                        <button id="toggleItalicButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Italic</button>
                        <button id="toggleUnderlineButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Underline</button>
                        <button id="toggleStrikeButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Strike</button>
                        <button id="toggleHighlightButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Highlight</button>
                        <button id="toggleCodeButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Code</button>

                        <button id="toggleTextSizeButton" data-dropdown-toggle="textSizeDropdown" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Text size</button>
                        <div id="textSizeDropdown" class="z-50 hidden bg-neutral-primary-medium border border-default-medium rounded-base shadow-lg w-64">
                            <ul class="p-2 space-y-1 text-sm text-body font-medium" aria-labelledby="toggleTextSizeButton">
                                <li><button data-text-size="16px" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">16px (Default)</button></li>
                                <li><button data-text-size="12px" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">12px (Tiny)</button></li>
                                <li><button data-text-size="14px" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">14px (Small)</button></li>
                                <li><button data-text-size="18px" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">18px (Lead)</button></li>
                                <li><button data-text-size="24px" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">24px (Large)</button></li>
                                <li><button data-text-size="36px" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">36px (Huge)</button></li>
                            </ul>
                        </div>

                        <button id="toggleTextColorButton" data-dropdown-toggle="textColorDropdown" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Text color</button>
                        <div id="textColorDropdown" class="z-50 hidden w-56 rounded-base bg-neutral-primary-soft p-2 border border-default-medium shadow-sm">
                            <div class="grid grid-cols-6 gap-2 group mb-3 items-center p-1.5 rounded hover:bg-neutral-tertiary">
                                <input type="color" id="color" value="#1A56DB" class="border-default-medium border bg-neutral-tertiary rounded p-px px-1 w-full h-8 col-span-3" />
                                <label for="color" class="text-body text-xs font-medium col-span-3 group-hover:text-heading">Pick color</label>
                            </div>
                            <div class="grid grid-cols-6 gap-1 mb-3">
                                <button type="button" data-hex-color="#1A56DB" style="background-color: #1A56DB" class="w-6 h-6 rounded-md"></button>
                                <button type="button" data-hex-color="#0E9F6E" style="background-color: #0E9F6E" class="w-6 h-6 rounded-md"></button>
                                <button type="button" data-hex-color="#F05252" style="background-color: #F05252" class="w-6 h-6 rounded-md"></button>
                                <button type="button" data-hex-color="#FF8A4C" style="background-color: #FF8A4C" class="w-6 h-6 rounded-md"></button>
                                <button type="button" data-hex-color="#0694A2" style="background-color: #0694A2" class="w-6 h-6 rounded-md"></button>
                                <button type="button" data-hex-color="#111928" style="background-color: #111928" class="w-6 h-6 rounded-md"></button>
                            </div>
                            <button id="reset-color" type="button" class="w-full text-xs text-body border border-default-medium rounded-base px-2 py-1.5 hover:bg-neutral-tertiary-medium hover:text-heading">Reset color</button>
                        </div>

                        <button id="toggleFontFamilyButton" data-dropdown-toggle="fontFamilyDropdown" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Font</button>
                        <div id="fontFamilyDropdown" class="z-50 hidden bg-neutral-primary-medium border border-default-medium rounded-base shadow-lg w-52">
                            <ul class="p-2 space-y-1 text-sm text-body font-medium" aria-labelledby="toggleFontFamilyButton">
                                <li><button data-font-family="Inter, ui-sans-serif" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Default</button></li>
                                <li><button data-font-family="Arial, sans-serif" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded" style="font-family: Arial, sans-serif;">Arial</button></li>
                                <li><button data-font-family="'Courier New', monospace" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded" style="font-family: 'Courier New', monospace;">Courier New</button></li>
                                <li><button data-font-family="Georgia, serif" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded" style="font-family: Georgia, serif;">Georgia</button></li>
                                <li><button data-font-family="Tahoma, sans-serif" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded" style="font-family: Tahoma, sans-serif;">Tahoma</button></li>
                                <li><button data-font-family="'Times New Roman', serif" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded" style="font-family: 'Times New Roman', serif;">Times New Roman</button></li>
                            </ul>
                        </div>

                        <button id="toggleLeftAlignButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Left</button>
                        <button id="toggleCenterAlignButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Center</button>
                        <button id="toggleRightAlignButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Right</button>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="relative">
                            <button id="typographyDropdownButton" class="flex items-center justify-center text-body bg-neutral-primary-strong border border-default-strong hover:bg-neutral-secondary-strongest hover:text-heading focus:ring-4 focus:ring-neutral-tertiary-soft shadow-xs font-medium leading-5 rounded-base text-xs px-3 py-1.5 focus:outline-none" type="button">Format</button>
                            <div id="typographyDropdown" class="z-[70] hidden absolute left-0 top-full mt-1 bg-neutral-primary-medium border border-default-medium rounded-base shadow-lg w-72">
                                <ul class="p-2 space-y-1 text-sm text-body font-medium" aria-labelledby="typographyDropdownButton">
                                    <li><button id="toggleParagraphButton" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Paragraph</button></li>
                                    <li><button data-heading-level="1" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Heading 1</button></li>
                                    <li><button data-heading-level="2" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Heading 2</button></li>
                                    <li><button data-heading-level="3" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Heading 3</button></li>
                                    <li><button data-heading-level="4" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Heading 4</button></li>
                                    <li><button data-heading-level="5" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Heading 5</button></li>
                                    <li><button data-heading-level="6" type="button" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Heading 6</button></li>
                                </ul>
                            </div>
                        </div>

                        <button type="button" data-modal-target="advanced-image-modal" data-modal-toggle="advanced-image-modal" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Insert image</button>
                        <button id="toggleListButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Bullets</button>
                        <button id="toggleOrderedListButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Numbers</button>
                        <button id="toggleBlockquoteButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Quote</button>
                        <button id="toggleHRButton" type="button" class="p-1.5 text-xs text-body rounded-sm cursor-pointer hover:text-heading hover:bg-neutral-quaternary">Rule</button>
                    </div>
                </div>

                <div class="px-4 py-3 bg-neutral-primary rounded-b-base">
                    <label for="wysiwyg-example" class="sr-only">Article content</label>
                    <div id="wysiwyg-example" class="block w-full px-0 text-sm text-body bg-neutral-primary border-0 focus:ring-0 min-h-[18rem]"></div>
                </div>
            </div>

            <p class="mt-2 text-xs text-body" id="editorDropHint">Drag and drop images into the editor to upload and insert at cursor.</p>
            <p id="contentError" class="mt-2 text-sm text-red-600 hidden"></p>
        </div>
    </section>
</form>

<div id="advanced-image-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-neutral-primary-soft border border-default rounded-base shadow-sm p-4 md:p-6">
            <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                <h3 class="text-lg font-medium text-heading">Insert advanced image</h3>
                <button type="button" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center" data-modal-hide="advanced-image-modal">
                    <span class="sr-only">Close modal</span>
                    &times;
                </button>
            </div>

            <form id="advancedImageForm" class="pt-4 md:pt-6">
                <div class="grid gap-4 grid-cols-1">
                    <div>
                        <label for="advancedImageFile" class="block mb-2.5 text-sm font-medium text-heading">Upload image file</label>
                        <input type="file" id="advancedImageFile" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif" class="block w-full text-sm text-body file:mr-4 file:py-2 file:px-4 file:rounded-base file:border-0 file:text-sm file:font-medium file:bg-neutral-tertiary-medium file:text-heading hover:file:bg-neutral-quaternary" />
                        <p class="mt-1 text-xs text-body">Allowed: JPG, JPEG, PNG, WEBP, GIF. Max 5MB.</p>
                    </div>
                    <div class="text-xs text-body">or provide an image URL</div>
                    <div>
                        <label for="image-url" class="block mb-2.5 text-sm font-medium text-heading">Image URL</label>
                        <input type="url" name="image_url" id="image-url" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="https://placehold.co/600x400" />
                    </div>
                    <div>
                        <label for="image-alt" class="block mb-2.5 text-sm font-medium text-heading">Image alt</label>
                        <input type="text" name="alt_text" id="image-alt" maxlength="255" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" />
                    </div>
                    <div>
                        <label for="image-title" class="block mb-2.5 text-sm font-medium text-heading">Image title</label>
                        <input type="text" name="title" id="image-title" maxlength="255" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" />
                    </div>
                    <p id="advancedImageError" class="text-sm text-red-600 hidden"></p>
                </div>

                <button type="submit" class="mt-4 w-full inline-flex items-center justify-center text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Insert image</button>
            </form>
        </div>
    </div>
</div>

<script src="<?= App::assetPath('js/reporter-article-editor.js') ?>"></script>
