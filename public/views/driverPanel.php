<?php
require __DIR__ . '/../../vendor/autoload.php';

$user = $_COOKIE['user_spedycja'];

// Stały ID kierowcy dla celów demonstracyjnych
$driverId = 1;
?>

<form id="form-driver-panel">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-truck me-2"></i>Panel Kierowcy
                    <small class="text-muted">(ID: <?= $driverId ?>)</small>
                </h5>
                <button type="button" class="btn-close" data-mdb-ripple-init data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="container">
                    <!-- Statystyki kierowcy na górze -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-primary text-white">
                                <div class="card-header">
                                    <h6 class="fw-bold mb-0 text-white">
                                        <i class="fas fa-chart-line me-2"></i>Statystyki kierowcy
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <h3 class="mb-0" id="totalOrdersCount">0</h3>
                                            <small>Wszystkie zlecenia</small>
                                        </div>
                                        <div class="col-md-3">
                                            <h3 class="mb-0" id="activeOrdersCount">0</h3>
                                            <small>Aktywne zlecenia</small>
                                        </div>
                                        <div class="col-md-3">
                                            <h3 class="mb-0" id="completedOrdersCount">0</h3>
                                            <small>Ukończone</small>
                                        </div>
                                        <div class="col-md-3">
                                            <h3 class="mb-0" id="totalDistanceKm">0</h3>
                                            <small>km przejechane</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wybór aktywnego zlecenia -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="fw-bold mb-0">
                                        <i class="fas fa-clipboard-list me-2"></i>Wybierz aktywne zlecenie
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <select id="activeOrderSelect" class="form-select" data-mdb-select-init>
                                        <option value="" selected>Wybierz zlecenie do zarządzania</option>
                                        <!-- Opcje będą dodane dynamicznie -->
                                    </select>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Dostępne są tylko zlecenia przypisane do Ciebie
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel zarządzania wybranym zleceniem (ukryty początkowo) -->
                    <div id="orderManagementPanel" style="display: none;">
                        
                        <!-- Informacje o wybranym zleceniu -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="fw-bold mb-0 text-white">
                                            <i class="fas fa-info-circle me-2"></i>Szczegóły wybranego zlecenia
                                        </h6>
                                    </div>
                                    <div class="card-body" id="selectedOrderDetails">
                                        <!-- Szczegóły będą dodane dynamicznie -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Akcje związane ze zleceniem -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="fw-bold mb-0">
                                            <i class="fas fa-play-circle me-2"></i>Zarządzanie dostawą
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="deliveryActions">
                                            <!-- Przyciski będą dodane dynamicznie w zależności od statusu -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="fw-bold mb-0">
                                            <i class="fas fa-map-marker-alt me-2"></i>Lokalizacja
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-outline" data-mdb-input-init>
                                                    <input type="number" id="latitude" class="form-control" step="0.000001" placeholder="52.2297">
                                                    <label class="form-label" for="latitude">Szerokość</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-outline" data-mdb-input-init>
                                                    <input type="number" id="longitude" class="form-control" step="0.000001" placeholder="21.0122">
                                                    <label class="form-label" for="longitude">Długość</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <button type="button" class="btn btn-outline-success w-100" data-action="get-location" data-mdb-ripple-init>
                                                    <i class="fas fa-crosshairs me-1"></i>Pobierz GPS
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" class="btn btn-primary w-100" data-action="update-location" data-mdb-ripple-init>
                                                    <i class="fas fa-map-marker-alt me-1"></i>Aktualizuj
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Podpis dla zakończenia dostawy (ukryty początkowo) -->
                        <div id="completionPanel" class="row mb-4" style="display: none;">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning">
                                        <h6 class="fw-bold mb-0">
                                            <i class="fas fa-signature me-2"></i>Zakończenie dostawy
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-outline" data-mdb-input-init>
                                            <input type="text" id="signature" class="form-control" placeholder="Imię i nazwisko odbiorcy">
                                            <label class="form-label" for="signature">Podpis/Potwierdzenie odbioru</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista wszystkich zleceń kierowcy -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="fw-bold mb-0">
                                        <i class="fas fa-list me-2"></i>Wszystkie moje zlecenia
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="allOrdersList">
                                        <!-- Lista będzie załadowana dynamicznie -->
                                        <div class="text-center p-3">
                                            <i class="fas fa-spinner fa-spin"></i> Ładowanie zleceń...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
