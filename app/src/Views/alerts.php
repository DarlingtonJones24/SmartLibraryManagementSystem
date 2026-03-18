<div class="container py-4">
	<h3 class="mb-3"><?= htmlspecialchars($notificationsViewModel->title ?? 'Notifications') ?></h3>

	<div class="card card-soft p-3 notifications">
		<?php if (!empty($notificationsViewModel->notifications)): ?>
			<?php foreach ($notificationsViewModel->notifications as $notification): ?>
				<div class="notification-item notification-item-<?= htmlspecialchars($notification['type']) ?>">
					<div>
						<div class="d-flex flex-wrap align-items-center gap-2 mb-1">
							<span class="badge text-bg-<?= htmlspecialchars($notification['badgeClass']) ?>">
								<?= htmlspecialchars($notification['label']) ?>
							</span>
							<?php if ($notification['date'] !== ''): ?>
								<div class="meta small text-muted"><?= htmlspecialchars($notification['date']) ?></div>
							<?php endif; ?>
						</div>
						<div><?= htmlspecialchars($notification['message']) ?></div>
					</div>

					<?php if ($notification['link'] !== ''): ?>
						<a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars($notification['link']) ?>">Open</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="notification-item">
				<div>
					<div class="fw-semibold">No new alerts</div>
					<div class="meta small text-muted">You are all caught up.</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
