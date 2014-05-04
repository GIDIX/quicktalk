<?php
	Templates::display('header');
?>

	<h1>Forums</h1>

	<div id="forum">
	<?php
		foreach (Templates::getVar('categories') as $category) {
	?>
		<div class="category">

			<h2><?php echo $category->getTitle(); ?></h2>

			<div class="forums">
				<?php
					$forums = $category->getForums();

					foreach ($forums as $forum) {
				?>

					<div class="forum">
						<div class="icon"><img src="<?php echo Templates::getThemeURL(); ?>images/icons/topics/<?php echo $forum->getIcon(); ?>.svg" alt="" /></div>

						<div class="title">
							<h3><a href="<?php echo URLController::get($forum); ?>"><?php echo $forum->getTitle(); ?></a></h3>
							
							<span>
								<?php echo $forum->getDescription(); ?>
							</span>
						</div>

						<div class="count">
							<span><?php echo $forum->getTopicsCount(); ?></span>
							TOPICS
						</div>

						<div class="lastPost">
							<?php
								$lastPost = $forum->getLastPost();

								if ($lastPost instanceof ForumPost) {
							?>
							
								<a href="<?php echo URLController::get($lastPost); ?>"><b><?php echo $lastPost->getTopic()->getShortTitle(32); ?></b></a>
								by <?php echo ThemeFunctions::getUserURL($lastPost); ?><br />
								<small class="grey"><?php echo $lastPost->getFormattedDate(); ?></small>
								
							<?php } else { ?>
								-
							<?php } ?>
						</div>
					</div>

				<?php
					}
				?>
			</div>
		</div>
	<?php
		}
	?>
	</div>

<?php
	Templates::display('footer');
?>