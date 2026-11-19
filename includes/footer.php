</main>
<footer>© <?= date('Y') ?> <?= e(setting('site_name')) ?>. Built with HTML, CSS, JavaScript, PHP and MySQL.</footer>
</div>
<?php if (!empty($withModal)): ?>
<div id="modal" role="dialog" aria-label="Project image"><div><img src="" alt=""><p></p></div></div>
<?php endif; ?>
<button id="toTop" aria-label="Scroll to top">↑</button>
<script src="js/main.js"></script>
<?php foreach (($extraScripts ?? []) as $s): ?><script src="<?= e($s) ?>"></script>
<?php endforeach; ?>
</body>
</html>
