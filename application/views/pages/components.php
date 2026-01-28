<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>
<div id="components-page" class="backend-page">
    <p class="mb-6 text-sm text-slate-600">
        This page collects the canonical UI components used across the admin experience.
    </p>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-5">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-medium text-slate-900">Buttons</h3>
                <span class="text-xs font-medium uppercase text-slate-400">Actions</span>
            </div>
            <div class="flex flex-wrap gap-3">
                <button class="btn btn-primary" type="button">Primary</button>
                <button class="btn btn-secondary" type="button">Secondary</button>
                <button class="btn btn-outline-secondary" type="button">Outline</button>
                <button class="btn btn-outline-primary" type="button">Ghost</button>
                <button class="btn btn-primary" type="button" disabled>Disabled</button>
            </div>
            <div class="mt-4 flex flex-wrap gap-3">
                <button class="btn btn-primary btn-sm" type="button">Small</button>
                <button class="btn btn-secondary btn-sm" type="button">Small</button>
                <button class="btn btn-primary btn-lg" type="button">Large</button>
            </div>
        </section>

        <section class="rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-5">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-medium text-slate-900">Form Controls</h3>
                <span class="text-xs font-medium uppercase text-slate-400">Inputs</span>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label" for="components-text-input">Text Input</label>
                    <input id="components-text-input" class="form-control" placeholder="Enter text">
                </div>
                <div>
                    <label class="form-label" for="components-email-input">Email</label>
                    <input id="components-email-input" type="email" class="form-control" placeholder="name@example.com">
                </div>
                <div>
                    <label class="form-label" for="components-select">Select</label>
                    <select id="components-select" class="form-select">
                        <option>Default</option>
                        <option>Option one</option>
                        <option>Option two</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="components-date">Date</label>
                    <input id="components-date" type="date" class="form-control">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label" for="components-textarea">Textarea</label>
                    <textarea id="components-textarea" class="form-control" rows="3"
                              placeholder="Longer form content"></textarea>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-5">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-medium text-slate-900">Selections</h3>
                <span class="text-xs font-medium uppercase text-slate-400">Toggles</span>
            </div>
            <div class="space-y-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="components-check">
                    <label class="form-check-label" for="components-check">Checkbox</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="components-radio" id="components-radio-1"
                           checked>
                    <label class="form-check-label" for="components-radio-1">Radio selected</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="components-radio" id="components-radio-2">
                    <label class="form-check-label" for="components-radio-2">Radio</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="components-switch">
                    <label class="form-check-label" for="components-switch">Switch</label>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-5">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-medium text-slate-900">Badges & Alerts</h3>
                <span class="text-xs font-medium uppercase text-slate-400">Status</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="badge bg-success">Success</span>
                <span class="badge bg-warning text-dark">Warning</span>
                <span class="badge bg-danger">Danger</span>
                <span class="badge bg-info text-dark">Info</span>
                <span class="badge bg-secondary">Neutral</span>
            </div>
            <div class="mt-4 space-y-3">
                <div class="alert alert-success mb-0">Success alert with supporting text.</div>
                <div class="alert alert-warning mb-0">Warning alert with supporting text.</div>
                <div class="alert alert-danger mb-0">Danger alert with supporting text.</div>
            </div>
        </section>

        <section class="rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-5">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-medium text-slate-900">Navigation</h3>
                <span class="text-xs font-medium uppercase text-slate-400">Tabs</span>
            </div>
            <div class="mb-4">
                <ul class="booking-tab-line-list" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="booking-tab-line active" type="button" role="tab" aria-selected="true">
                            Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="booking-tab-line" type="button" role="tab" aria-selected="false" tabindex="-1">
                            Details
                        </button>
                    </li>
                </ul>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                    Dropdown
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else</a>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-5 lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-medium text-slate-900">Cards & Tables</h3>
                <span class="text-xs font-medium uppercase text-slate-400">Layouts</span>
            </div>
            <div class="grid gap-4 lg:grid-cols-3">
                <div class="card border bg-white">
                    <div class="card-body">
                        <div class="text-sm text-slate-500">Card title</div>
                        <div class="text-lg font-medium text-slate-900">Primary metric</div>
                        <div class="mt-2 text-sm text-slate-600">Supporting description text.</div>
                    </div>
                </div>
                <div class="card border bg-white">
                    <div class="card-body">
                        <div class="text-sm text-slate-500">Card title</div>
                        <div class="text-lg font-medium text-slate-900">Secondary metric</div>
                        <div class="mt-2 text-sm text-slate-600">Supporting description text.</div>
                    </div>
                </div>
                <div class="card border bg-white">
                    <div class="card-body">
                        <div class="text-sm text-slate-500">Card title</div>
                        <div class="text-lg font-medium text-slate-900">Tertiary metric</div>
                        <div class="mt-2 text-sm text-slate-600">Supporting description text.</div>
                    </div>
                </div>
            </div>
            <div class="mt-4 card border bg-white">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Status</th>
                            <th class="pe-4">Updated</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="ps-4">Component item</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td class="pe-4 text-slate-500">Just now</td>
                        </tr>
                        <tr>
                            <td class="ps-4">Component item</td>
                            <td><span class="badge bg-warning text-dark">Paused</span></td>
                            <td class="pe-4 text-slate-500">2 hours ago</td>
                        </tr>
                        <tr>
                            <td class="ps-4">Component item</td>
                            <td><span class="badge bg-secondary">Draft</span></td>
                            <td class="pe-4 text-slate-500">Yesterday</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
<?php end_section('content'); ?>
