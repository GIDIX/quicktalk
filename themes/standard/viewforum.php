<?php
	Templates::display('header');

	$forum = Templates::getVar('forum');
	$topics = Templates::getVar('topics');
?>
	
	<h1><?php echo $forum->getTitle(); ?></h1>

	<div id="forum">
		<div class="topics">
	<?php
		if (count($topics) == 0) {
	?>
		<div class="nothing">
			<h2><?php echo ForumT::get('no_topics'); ?></h2>
		</div>
	<?php
		}

		foreach ($topics as $topic) {
	?>

		<div class="topic">
			<div class="icon"><img src="<?php echo Templates::getThemeURL(); ?>images/icons/topics/<?php echo $forum->getIcon(); ?>.svg" alt="" /></div>

			<div class="title">
				<h3><a href="<?php echo URLController::get($topic); ?>"><?php echo $topic->getTitle(); ?></a></h3>
				
				<span>
					<?php echo $topic->getFirstPost()->getShortContent(256); ?>
				</span>
			</div>

			<div class="count">
				<span><?php echo $topic->getPostsCount(); ?></span>
				POSTS
			</div>

			<div class="lastPost">
				<?php
					$lastPost = $topic->getLastPost();

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
	Templates::display('footer');
?>