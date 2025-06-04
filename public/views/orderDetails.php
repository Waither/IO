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
                        if ($order["price"] != null) {
                            ?>
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <h5 class="text-center fw-bold">Cena</h5>
                                    <h5 class="text-center"><?= $order["price"]; ?> PLN</h5>
                                </div>
                            </div>
                            <?php
                        }

                        if ($order["driver"] != null) {
                            ?>
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <h5 class="text-center fw-bold">Kierowca</h5>
                                    <h5 class="text-center"><?= $order["driver"]; ?></h5>
                                </div>
                            </div>
                            <?php
                        }

                        if ($_COOKIE["user_spedycja"] == "Spedytor" && $order["status"] == "Utworzone") {
                            ?>
                            <input type="hidden" name="orderId" value="<?= $order["ID_order"]; ?>">
                            <div class="row mt-5">
                                <div class="col-12 mb-2">
                                    <h5 class="text-center fw-bold">Spedytor</h5>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text fw-bold">Cena</span>
                                        <div class="form-outline" data-mdb-input-init>
                                            <input type="number" id="price" class="form-control" name="price" min="0" step="0.01" required>
                                        </div>
                                        <span class="input-group-text fw-bold">PLN</span>
                                        <button type="submit" class="btn btn-success fw-bold" style="width: 220px;" data-mdb-ripple-init><i class="fas fa-check me-1"></i>Akceptuj</button>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        elseif ($_COOKIE["user_spedycja"] == "Spedytor" && ($order["status"] == "Zatwierdzone przez spedytora" || $order["status"] == "Zaakceptowane przez klienta") && $order["driver"] == null) {
                            ?>
                            <input type="hidden" name="orderId" value="<?= $order["ID_order"]; ?>">
                            <div class="row mt-5">
                                <div class="col-12 mb-2">
                                    <h5 class="text-center fw-bold">Wybór kierowcy</h5>
                                    <div class="input-group mb-3">
                                        <select id="driverId" class="w-100" name="driverId" data-mdb-select-init data-mdb-filter="true">
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
                        elseif ($_COOKIE["user_spedycja"] == "Klient" && $order["status"] == "Zatwierdzone przez spedytora") {
                            ?>
                            <input type="hidden" name="orderId" value="<?= $order["ID_order"]; ?>">
                            <div class="row mt-5">
                                <div class="col-12 mb-2">
                                    <h5 class="text-center fw-bold">Akceptacja zlecenia</h5>
                                    <button type="submit" class="btn btn-success w-100" data-mdb-ripple-init><i class="fas fa-check me-1"></i>Akceptuj</button>
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                </div>
            </div>
            <?php
                
            ?>
        </div>
    </div>
</form>