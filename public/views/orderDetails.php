<?php
    require __DIR__ . '/../../vendor/autoload.php';

    use App\Query\GetDriversQuery;
    use App\QueryHandler\GetDriversHandler;


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
                            <h5 class="text-center"><?= $order["company"]; ?></h5>
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
                        <div class="12 mb-2">
                            <h5 class="text-center fw-bold">Ładunek</h5>
                            <div class="input-group">
                                <div class="input-group-text w-100"><div class="w-100 text-center fw-bold"><?= $order["cargo"]; ?></div></div>
                                <div class="input-group-text w-100"><div class="w-100 text-center fw-bold"><?= $order["weight"]; ?></div></div>
                                <span class="input-group-text fw-bold">kg</span>
                            </div>
                        </div>
                    </div>

                    <?php
                        if ($order["status"] == "Zatwierdzone przez spedytora" && $order["driver"] == null) {
                            ?>
                            <input type="hidden" name="orderId" value="<?= $order["ID_order"]; ?>">
                            <div class="row mt-5">
                                <div class="col-12 mb-2">
                                    <h5 class="text-center fw-bold">Wybór kierowcy</h5>
                                    <div class="input-group mb-3">
                                        <select id="driverId" class="w-100" name="driverId" data-mdb-select-init data-mdb-filter="true" required>
                                            <option value="" selected hidden disabled></option>
                                            <?php
                                                $query = new GetDriversQuery();
                                                $handler = new GetDriversHandler();
                                                $drivers = $handler->handle($query);
                                                foreach ($drivers as $driver) {
                                                    ?>
                                                    <option value="<?= $driver["ID_driver"]; ?>"><?= $driver["name_driver"]; ?></option>
                                                    <?php
                                                }
                                            ?>
                                        </select>
                                        <button type="submit" class="btn btn-success">Przypisz</button>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    ?>
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