<form id="form-new-order">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Nowe Zlecenie</h5>
                <button type="button" class="btn-close" data-mdb-ripple-init data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-column gap-3">
                    <div id="async" class="form-outline mb-2 autocomplete" data-mdb-input-init>
                        <input type="text" id="company" class="form-control" name="company" required data-mdb-showcounter="true" maxlength="100">
                        <label class="form-label" for="company">Firma</label>
                        <div class="form-helper"></div>
                    </div>
                    <div class="input-group mb-2">
                        <div class="form-outline" data-mdb-input-init>
                            <input type="text" id="pickup" class="form-control" name="pickup" data-mdb-showcounter="true" maxlength="100" required>
                            <label class="form-label" for="pickup">Miejsce odbioru</label>
                            <div class="form-helper"></div>
                        </div>
                        <span class="input-group-text"><i class="fas fa-arrow-right"></i></span>
                        <div class="form-outline" data-mdb-input-init>
                            <input type="text" id="delivery" class="form-control" name="delivery" data-mdb-showcounter="true" maxlength="100" required>
                            <label class="form-label" for="delivery">Miejsce dostawy</label>
                            <div class="form-helper"></div>
                        </div>
                    </div>

                    <div class="input-group mb-2">
                        <div class="form-outline" data-mdb-input-init>
                            <input type="text" id="cargo" class="form-control" name="cargo" data-mdb-showcounter="true" maxlength="100" required>
                            <label class="form-label" for="cargo">Ładunek</label>
                            <div class="form-helper"></div>
                        </div>
                        <div class="form-outline" data-mdb-input-init>
                            <input type="number" id="weight" class="form-control" name="weight" min="0" step="0.001" max="100000" required>
                            <label class="form-label" for="weight">Waga</label>
                        </div>
                        <span class="input-group-text">kg</span>
                    </div>
                    
                    
                </div>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="submit" class="btn btn-success fw-bold" data-mdb-ripple-init><i class="fas fa-check me-1"></i>Stwórz</button>
            </div>
        </div>
    </div>
</form>