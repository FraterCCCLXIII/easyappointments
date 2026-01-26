<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>
<div id="billing-page" class="container backend-page">
    <div class="row">
        <div class="col-12">
            <div class="rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="pe-4">Stripe ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No transactions found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td class="ps-4"><?= date('Y-m-d H:i', strtotime($transaction['book_datetime'])) ?></td>
                                        <td><?= e($transaction['first_name'] . ' ' . $transaction['last_name']) ?></td>
                                        <td><?= e($transaction['service_name']) ?></td>
                                        <td><?= number_format($transaction['payment_amount'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $transaction['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($transaction['payment_status']) ?>
                                            </span>
                                        </td>
                                        <td class="pe-4">
                                            <small class="text-muted"><?= e($transaction['stripe_payment_intent_id']) ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php end_section('content'); ?>
