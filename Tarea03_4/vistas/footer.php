<footer class="footer">
    <div class="container-footer">
        <p>&copy; José Alfredo Bautista Sebastiao - <?= date("Y"); ?></p>
    </div>
</footer>
</body>
<?php
ob_end_flush(); //Finaliza el buffer de salida que se inicia en el header
