<?php
	Templates::display('header');

	$forum = Templates::getVar('forum');
	$topic = Templates::getVar('topic');
	$posts = Templates::getVar('posts');
	$pages = Templates::getVar('pages');

	$i = 0;
?>
	
	<h1>
		<a href="<?php echo URLController::get($forum); ?>">
			<?php echo $forum->getTitle(); ?>
		</a> &rsaquo;

		<?php echo $topic->getTitle(); ?>
	</h1>

	<div id="forum">
		<div id="topic">
			<?php foreach ($posts as $post): $i++; ?>
				<?php if ($i == 1): ?>
					<div class="post first">
						<div class="meta">
							<?php
								$author = $post->getUser();

								if ($author instanceof UserReadOnly):
									// User exists
							?>
								<div class="avatar">
									<img src="<?php echo $author->getAvatar(); ?>" />
								</div>

								<div class="userinfo">
									<h2><a href="<?php echo URLController::get($author); ?>">
										<?php echo $author->getUsername(); ?>
									</a></h2>

									<time><?php echo date('d.m.Y, H:i', $post->getDate()); ?></time>
								</div>
							<?php else: ?>
								<div class="avatar">
									<img src="<?php echo UserReadOnly::getAvatarDirectory() . Config::get('default_avatar'); ?>" />
								</div>

								<div class="userinfo">
									<h2><?php echo GeneralT::get('unknown'); ?></h2>

									<time><?php echo date('d.m.Y, H:i', $post->getDate()); ?></time>
								</div>
							<?php endif; ?>

							<div class="actions">
								...
							</div>
						</div>

						<div class="content">
							<?php echo $post->getContent(); ?>
						</div>
					</div>
				<?php else: ?>

				<?php endif; ?>
			<?php $i++; endforeach; ?>
		</div>
	</div>

<?php
	Templates::display('footer');
?>