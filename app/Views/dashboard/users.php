<?php
/**
 * View: dashboard/users.php
 * Manage Users page for Admin.
 * Loaded inside the dashboard layout via $content.
 */
?>

<div class="flex items-center justify-between mb-4">
    <h2 class="text-xl font-bold text-neutral-900">Manage Users</h2>
</div>

<div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
    <table class="w-full text-sm text-left rtl:text-right text-body">
        <thead class="text-sm text-body bg-neutral-secondary-soft border-b border-default">
            <tr>
                <th scope="col" class="px-6 py-3 font-medium">User</th>
                <th scope="col" class="px-6 py-3 font-medium">Email</th>
                <th scope="col" class="px-6 py-3 font-medium">Role</th>
                <th scope="col" class="px-6 py-3 font-medium">Joined Date</th>
                <th scope="col" class="px-6 py-3 font-medium text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $u): ?>
                    <tr class="odd:bg-neutral-primary even:bg-neutral-secondary-soft border-b border-default">
                        <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <?php if (!empty($u['avatar_path'])): ?>
                                    <img class="w-10 h-10 rounded-full object-cover shrink-0" src="<?= url($u['avatar_path']) ?>" alt="<?= htmlspecialchars($u['name']) ?> Avatar">
                                <?php else: ?>
                                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-full shrink-0 bg-neutral-tertiary text-body">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="font-semibold text-heading"><?= htmlspecialchars($u['name']) ?></div>
                                </div>
                            </div>
                        </th>
                        <td class="px-6 py-4">
                            <?= htmlspecialchars($u['email']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <button
                                id="roleDropdownButton-<?= $u['id'] ?>"
                                data-dropdown-toggle="roleDropdown-<?= $u['id'] ?>"
                                data-dropdown-placement="bottom-end"
                                class="inline-flex items-center justify-between min-w-32 text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-base text-sm px-3 py-2 focus:outline-none"
                                type="button"
                            >
                                <span id="currentRoleText-<?= $u['id'] ?>"><?= htmlspecialchars($u['role_name'] ?? 'User') ?></span>
                                <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                </svg>
                            </button>

                            <div id="roleDropdown-<?= $u['id'] ?>" class="z-10 hidden bg-neutral-primary-medium border border-default-medium rounded-base shadow-lg w-44">
                                <ul class="p-2 text-sm text-body font-medium" aria-labelledby="roleDropdownButton-<?= $u['id'] ?>">
                                    <?php foreach (['Admin', 'Editor', 'Reporter', 'Reader'] as $roleBaseName): ?>
                                        <li>
                                            <a
                                                href="#"
                                                class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded role-change-btn"
                                                data-user-id="<?= $u['id'] ?>"
                                                data-role="<?= $roleBaseName ?>"
                                                data-dropdown-id="roleDropdown-<?= $u['id'] ?>"
                                            >
                                                <?= $roleBaseName ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?= date('M d, Y', strtotime($u['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-3">
                                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-base text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100" title="Edit User Details" aria-label="Edit User Details">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-base text-red-600 hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-100" title="Delete User" aria-label="Delete User">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="bg-neutral-primary">
                    <td colspan="5" class="px-6 py-10 text-center text-body">
                        No users found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="fixed bottom-5 right-5 z-50">
    <div id="toast-success" class="hidden items-center w-full max-w-sm p-4 text-body bg-neutral-primary-soft rounded-base shadow-xs border border-default" role="alert">
        <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 text-fg-success bg-success-soft rounded">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
            </svg>
            <span class="sr-only">Check icon</span>
        </div>
        <div class="ms-3 text-sm font-normal" id="toast-success-message">Role changed successfully.</div>
        <button type="button" class="ms-auto flex items-center justify-center text-body hover:text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded text-sm h-8 w-8 focus:outline-none" data-dismiss-target="#toast-success" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
            </svg>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof initFlowbite === 'function') {
        initFlowbite();
    }

    const toastEl = document.getElementById('toast-success');
    const toastMessageEl = document.getElementById('toast-success-message');
    let toastTimeout;

    const showSuccessToast = (message) => {
        toastMessageEl.textContent = message;
        toastEl.classList.remove('hidden');
        toastEl.classList.add('flex');

        clearTimeout(toastTimeout);
        toastTimeout = window.setTimeout(() => {
            toastEl.classList.add('hidden');
            toastEl.classList.remove('flex');
        }, 3000);
    };

    document.querySelectorAll('.role-change-btn').forEach((btn) => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();

            const userId = btn.getAttribute('data-user-id');
            const newRole = btn.getAttribute('data-role');
            const dropdownId = btn.getAttribute('data-dropdown-id');

            try {
                const response = await fetch('<?= url('/admin/users/change-role') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ user_id: userId, role: newRole })
                });

                const data = await response.json();

                if (!data.success) {
                    alert(data.error || 'Failed to update role');
                    return;
                }

                document.getElementById(`currentRoleText-${userId}`).textContent = newRole;

                if (window.FlowbiteInstances && dropdownId) {
                    const dropdown = FlowbiteInstances.getInstance('Dropdown', dropdownId);
                    if (dropdown) {
                        dropdown.hide();
                    }
                }

                showSuccessToast('Role changed successfully.');
            } catch (error) {
                console.error(error);
                alert('An error occurred while updating the role.');
            }
        });
    });
});
</script>
