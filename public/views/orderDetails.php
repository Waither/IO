<?php
    if (!isset($_GET["order"])) {
        header("Location: /");
        exit();
    }
    $order = json_decode($_GET["order"], true);
?>
<form id="form-details">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Zlecenie ID<?= $order["ID_order"]; ?></h5>
                <button type="button" class="btn-close" data-mdb-ripple-init data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php var_dump($order); ?>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="submit" class="btn btn-success fw-bold" data-mdb-ripple-init>Akceptuj</button>
            </div>
        </div>
    </div>
</form>