			</div>
		</div>

		<footer>
			&copy; <?php echo date("Y"); ?> GIDIX &amp; IT-Talent
		</footer>
	</body>
</html>
<?php
	$content = ob_get_contents();
	ob_get_clean();

	$content = $token->auto_append($content);
	echo $content;
?>