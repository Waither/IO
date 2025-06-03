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
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <h5 class="text-center fw-bold mb-0">Firma</h5>
                            <h5 class="text-center"></h5>
                        </div>

                        <div class="col-md-6 mb-2">
                            <h5 class="text-center fw-bold mb-0">Status</h5>
                            <h5 class="text-center"><?= $order["status"]; ?></h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-2">
                            <h5 class="text-center fw-bold">Trasa</h5>
                            <div class="input-group mb-3">
                                <div class="input-group-text w-100"><div class="w-100 text-center fw-bold"><?= $order["location_from"]; ?></div></div>
                                <span class="input-group-text fw-bold"><i class="fas fa-arrow-right"></i></span>
                                <div class="input-group-text w-100"><div class="w-100 text-center fw-bold"><?= $order["location_to"]; ?></div></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">

                        </div>

                        <div class="col-md-6 mb-2">
                            
                        </div>
                    </div>
                </div>
            </div>
            <?php
                if ($order["status"] == "Utworzone") {
                    ?>
                    <div class="modal-footer justify-content-end">
                        <button type="submit" class="btn btn-success fw-bold" data-mdb-ripple-init><i class="fas fa-check me-1"></i>Akceptuj</button>
                    </div>
                    <?php
                }
            ?>
        </div>
    </div>
</form>