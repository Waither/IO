<form id="form-new-order">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Nowe Zlecenie</h5>
                <button type="button" class="btn-close" data-mdb-ripple-init data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-column gap-3">
                    <div class="form-outline mb-2" data-mdb-input-init>
                        <input type="text" id="pickup" class="form-control" name="pickup" data-mdb-showcounter="true" maxlength="100" required>
                        <label class="form-label" for="pickup">Miejsce odbioru</label>
                        <div class="form-helper"></div>
                    </div>
                    <div class="form-outline mb-2" data-mdb-input-init>
                        <input type="text" id="delivery" class="form-control" name="delivery" data-mdb-showcounter="true" maxlength="100" required>
                        <label class="form-label" for="delivery">Miejsce dostawy</label>
                        <div class="form-helper"></div>
                    </div>
                    <div class="form-outline mb-2" data-mdb-input-init>
                        <input type="text" id="cargo" class="form-control" name="cargo" data-mdb-showcounter="true" maxlength="100" required>
                        <label class="form-label" for="cargo">Ładunek</label>
                        <div class="form-helper"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="submit" class="btn btn-success fw-bold" data-mdb-ripple-init>Wyślij</button>
            </div>
        </div>
    </div>
</form>