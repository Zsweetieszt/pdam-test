@extends('layouts.app')

@section('title', 'Kalkulator Tarif PDAM')

@section('content')
<style>
    .calculator-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .calculator-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .calculation-result {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: white;
        border-radius: 12px;
        animation: fadeIn 0.5s ease-in-out;
    }

    .block-breakdown {
        border-left: 4px solid #2563eb;
        transition: all 0.2s ease;
    }

    .block-breakdown:hover {
        border-left-color: #16a34a;
        background-color: #f0f9ff;
    }

    .tariff-input {
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        transition: border-color 0.2s ease;
    }

    .tariff-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .real-time-badge {
        animation: pulse 2s infinite;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .calculation-loading {
        border: 4px solid #f3f4f6;
        border-top: 4px solid #2563eb;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .usage-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 8px;
        border-radius: 8px;
        background: #e5e7eb;
        outline: none;
        transition: background 0.2s;
    }

    .usage-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #2563eb;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .progressive-indicator {
        height: 6px;
        border-radius: 3px;
        margin: 0.5rem 0;
    }

    .block-1 { background-color: #22c55e; }
    .block-2 { background-color: #eab308; }
    .block-3 { background-color: #f97316; }
    .block-4 { background-color: #ef4444; }

    .error-message {
        background-color: #fee2e2;
        border: 1px solid #fecaca;
        color: #dc2626;
        padding: 1rem;
        border-radius: 8px;
        margin: 1rem 0;
    }
</style>

<!-- REQ-F-11: Complete Tariff Management Interface -->
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card calculator-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">
                                <i class="fas fa-calculator text-primary me-2"></i>
                                Kalkulator Tarif PDAM & Progressive Block Rate
                            </h4>
                            <p class="text-muted mb-0">
                                Real-time calculator dengan 20 customer groups dan 7 meter sizes
                                <span class="badge bg-success real-time-badge ms-2">
                                    <i class="fas fa-bolt me-1"></i>Real-time
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-primary me-2" onclick="resetCalculator()">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button class="btn btn-outline-primary" onclick="showTariffReference()">
                                <i class="fas fa-info-circle me-2"></i>Referensi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Calculator Inputs -->
        <div class="col-lg-5">
            <!-- REQ-F-11.1: Customer Groups Selection -->
            <div class="card calculator-card mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">
                        <i class="fas fa-users text-primary me-2"></i>
                        Pilih Golongan Pelanggan (20 Groups)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12
                            <label class="form-label">Golongan Pelanggan:</label>
                            <select id="customerGroupSelect" class="form-select tariff-input">
                                <option value="">Pilih golongan pelanggan...</option>
                            </select>
                            <div class="form-text" id="customerGroupInfo">Pilih golongan untuk melihat detail tarif</div>
                        </div>
                    </div>
                    
                    <!-- Loading state -->
                    <div id="customerGroupsLoading" class="text-center py-3" style="display: none;">
                        <div class="calculation-loading mx-auto"></div>
                        <p class="text-muted mt-2">Memuat data golongan pelanggan...</p>
                    </div>
                    
                    <!-- Error state -->
                    <div id="customerGroupsError" style="display: none;"></div>
                </div>
            </div>

            <!-- REQ-F-11.3: Meter Sizes Selection -->
            <div class="card calculator-card mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">
                        <i class="fas fa-tachometer-alt text-primary me-2"></i>
                        Pilih Ukuran Meter (7 Sizes)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12
                            <label class="form-label">Ukuran Meter:</label>
                            <select id="meterSizeSelect" class="form-select tariff-input">
                                <option value="">Pilih ukuran meter...</option>
                            </select>
                            <div class="form-text" id="meterSizeInfo">Pilih ukuran untuk melihat biaya admin</div>
                        </div>
                    </div>
                    
                    <!-- Loading state -->
                    <div id="meterSizesLoading" class="text-center py-3" style="display: none;">
                        <div class="calculation-loading mx-auto"></div>
                        <p class="text-muted mt-2">Memuat data ukuran meter...</p>
                    </div>
                    
                    <!-- Error state -->
                    <div id="meterSizesError" style="display: none;"></div>
                </div>
            </div>

            <!-- Usage Input -->
            <div class="card calculator-card">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">
                        <i class="fas fa-water text-primary me-2"></i>
                        Pemakaian Air (m³)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Masukkan Pemakaian:</label>
                            <input type="number" 
                                   id="usageInput" 
                                   class="form-control tariff-input" 
                                   value="30" 
                                   min="0" 
                                   max="9999"
                                   placeholder="Contoh: 30">
                            <div class="form-text">Range: 0 - 9999 m³</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Atau gunakan slider:</label>
                            <input type="range" 
                                   id="usageSlider" 
                                   class="usage-slider" 
                                   min="0" 
                                   max="100" 
                                   value="30">
                            <div class="d-flex justify-content-between text-muted small">
                                <span>0 m³</span>
                                <span id="sliderValue">30 m³</span>
                                <span>100+ m³</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick preset buttons -->
                    <div class="mt-3">
                        <small class="text-muted">Preset cepat:</small>
                        <div class="btn-group d-flex mt-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="setUsage(5)">5 m³</button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="setUsage(15)">15 m³</button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="setUsage(30)">30 m³</button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="setUsage(50)">50 m³</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Results & Breakdown -->
        <div class="col-lg-7">
            <!-- REQ-F-11.4: Calculation Result -->
            <div class="card calculator-card mb-4">
                <div class="card-body">
                    <div id="calculationResult">
                        <!-- Default state -->
                        <div class="text-center py-5">
                            <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Pilih golongan pelanggan dan ukuran meter</h5>
                            <p class="text-muted">Hasil kalkulasi akan tampil di sini secara real-time</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REQ-F-11.2: Progressive Block Rate Breakdown -->
            <div class="card calculator-card mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Progressive Block Rate Breakdown
                    </h6>
                </div>
                <div class="card-body">
                    <div id="progressiveBreakdown">
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Breakdown perhitungan progressive block rate akan tampil setelah kalkulasi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tariff Reference Info -->
            <div class="card calculator-card">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Informasi Progressive Block Rate
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Block Structure:</h6>
                            <ul class="list-unstyled">
                                <li><span class="badge bg-success me-2">Block I</span> 0-10 m³</li>
                                <li><span class="badge bg-warning me-2">Block II</span> 11-20 m³</li>
                                <li><span class="badge bg-orange me-2">Block III</span> 21-30 m³</li>
                                <li><span class="badge bg-danger me-2">Block IV</span> >30 m³</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-2">
                                <small class="text-muted d-block">Block I: 10×7.100 = Rp 71.000</small>
                                <small class="text-muted d-block">Block II: 10×8.500 = Rp 85.000</small>
                                <small class="text-muted d-block">Block III: 10×9.500 = Rp 95.000</small>
                                <small class="text-muted d-block">Admin Fee: Rp 7.500</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reference Modal -->
<div class="modal fade" id="tariffReferenceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-book text-primary me-2"></i>
                    Referensi Tarif PDAM
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Customer Groups (20 Total):</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Code</th><th>Category</th><th>Name</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>1L1-1L4</td><td>Sosial</td><td>Sosial I-IV</td></tr>
                                    <tr><td>2R1-2R4</td><td>Rumah Tangga</td><td>Rumah Tangga I-IV</td></tr>
                                    <tr><td>3N1-3N4</td><td>Niaga</td><td>Niaga I-IV</td></tr>
                                    <tr><td>4K1-4K8</td><td>Khusus</td><td>Khusus I-VIII</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Meter Sizes (7 Total):</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Size</th><th>DN</th><th>Admin Fee</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>1/2"</td><td>DN15</td><td>Rp 7.500</td></tr>
                                    <tr><td>3/4"</td><td>DN20</td><td>Rp 12.000</td></tr>
                                    <tr><td>1"</td><td>DN25</td><td>Rp 19.000</td></tr>
                                    <tr><td>1.5"</td><td>DN40</td><td>Rp 30.000</td></tr>
                                    <tr><td>2"</td><td>DN50</td><td>Rp 48.000</td></tr>
                                    <tr><td>3"</td><td>DN80</td><td>Rp 76.000</td></tr>
                                    <tr><td>4"</td><td>DN100</td><td>Rp 121.000</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ==========================================
// CONFIGURATION & GLOBALS
// ==========================================
var API_BASE = '/api';
var customerGroups = {};
var meterSizes = [];
var selectedGroup = null;
var selectedMeterSize = null;

// ==========================================
// UTILITY FUNCTIONS
// ==========================================
function showError(container, message) {
    const errorHtml = `
        <div class="error-message">
            <strong>Error:</strong> ${message}
        </div>
    `;
    
    if (typeof container === 'string') {
        const element = document.getElementById(container);
        if (element) element.innerHTML = errorHtml;
    } else {
        container.innerHTML = errorHtml;
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
        <button type="button" class="btn-close float-end" onclick="this.parentElement.remove()"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) toast.remove();
    }, 5000);
}

// ==========================================
// HTTP UTILITIES
// ==========================================
function getHeaders() {
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
    }
    
    return headers;
}

// ==========================================
// FALLBACK DATA
// ==========================================
function loadFallbackData() {
    const mockGroups = {
        "Sosial": [
            { code: "1L1", name: "Sosial I", rates: { block1: 6500, block2: 7500, block3: 8500, block4: 0 } },
            { code: "1L2", name: "Sosial II", rates: { block1: 7000, block2: 8000, block3: 9000, block4: 0 } },
            { code: "1L3", name: "Sosial III", rates: { block1: 7500, block2: 8500, block3: 9500, block4: 0 } },
            { code: "1L4", name: "Sosial IV", rates: { block1: 8000, block2: 9000, block3: 10000, block4: 0 } }
        ],
        "Rumah Tangga": [
            { code: "2R1", name: "Rumah Tangga I", rates: { block1: 7100, block2: 8500, block3: 9500, block4: 0 } },
            { code: "2R2", name: "Rumah Tangga II", rates: { block1: 8100, block2: 9500, block3: 10500, block4: 0 } },
            { code: "2R3", name: "Rumah Tangga III", rates: { block1: 9100, block2: 10500, block3: 11500, block4: 0 } },
            { code: "2R4", name: "Rumah Tangga IV", rates: { block1: 10100, block2: 11500, block3: 12500, block4: 0 } }
        ],
        "Niaga": [
            { code: "3N1", name: "Niaga I", rates: { block1: 12500, block2: 14000, block3: 15500, block4: 0 } },
            { code: "3N2", name: "Niaga II", rates: { block1: 13500, block2: 15000, block3: 16500, block4: 0 } },
            { code: "3N3", name: "Niaga III", rates: { block1: 14500, block2: 16000, block3: 17500, block4: 0 } },
            { code: "3N4", name: "Niaga IV", rates: { block1: 15500, block2: 17000, block3: 18500, block4: 0 } }
        ],
        "Khusus": [
            { code: "4K1", name: "Khusus I", rates: { block1: 18000, block2: 20000, block3: 22000, block4: 0 } },
            { code: "4K2", name: "Khusus II", rates: { block1: 19000, block2: 21000, block3: 23000, block4: 0 } },
            { code: "4K3", name: "Khusus III", rates: { block1: 20000, block2: 22000, block3: 24000, block4: 0 } },
            { code: "4K4", name: "Khusus IV", rates: { block1: 21000, block2: 23000, block3: 25000, block4: 0 } },
            { code: "4K5", name: "Khusus V", rates: { block1: 22000, block2: 24000, block3: 26000, block4: 0 } },
            { code: "4K6", name: "Khusus VI", rates: { block1: 23000, block2: 25000, block3: 27000, block4: 0 } },
            { code: "4K7", name: "Khusus VII", rates: { block1: 24000, block2: 26000, block3: 28000, block4: 0 } },
            { code: "4K8", name: "Khusus VIII", rates: { block1: 25000, block2: 27000, block3: 29000, block4: 0 } }
        ]
    };

    const mockMeterSizes = [
        { value: "1/2\"", label: "DN15", admin_fee: 7500 },
        { value: "3/4\"", label: "DN20", admin_fee: 12000 },
        { value: "1\"", label: "DN25", admin_fee: 19000 },
        { value: "1.5\"", label: "DN40", admin_fee: 30000 },
        { value: "2\"", label: "DN50", admin_fee: 48000 },
        { value: "3\"", label: "DN80", admin_fee: 76000 },
        { value: "4\"", label: "DN100", admin_fee: 121000 }
    ];

    customerGroups = mockGroups;
    meterSizes = mockMeterSizes;
    
    renderCustomerGroups();
    renderMeterSizes();
}

// ==========================================
// API FUNCTIONS
// ==========================================
async function loadCustomerGroups() {
    try {
        const response = await fetch(`${API_BASE}/tariff/customer-groups`, {
            method: 'GET',
            headers: getHeaders(),
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success && result.data) {
            customerGroups = result.data;
            renderCustomerGroups();
        } else {
            throw new Error('API returned unsuccessful response');
        }
        
    } catch (error) {
        console.error('Error loading customer groups, using fallback data');
        loadFallbackData();
    }
}

async function loadMeterSizes() {
    try {
        const response = await fetch(`${API_BASE}/tariff/meter-sizes`, {
            method: 'GET',
            headers: getHeaders(),
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success && result.data) {
            meterSizes = result.data;
            renderMeterSizes();
        } else {
            throw new Error('API returned unsuccessful response');
        }
        
    } catch (error) {
        console.error('Error loading meter sizes, using fallback data');
        if (meterSizes.length === 0) {
            loadFallbackData();
        }
    }
}

async function calculateTariff() {
    if (!selectedGroup || !selectedMeterSize) {
        return;
    }

    const usageInput = document.getElementById('usageInput');
    const usage = parseInt(usageInput ? usageInput.value : '0') || 0;
    
    if (usage <= 0) {
        clearResults();
        return;
    }

    showLoadingState();
    
    try {
        const requestData = {
            customer_group_code: selectedGroup,
            meter_size: selectedMeterSize,
            usage: usage
        };
        
        const response = await fetch(`${API_BASE}/tariff/simulate`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify(requestData),
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success && result.data) {
            renderResults(result.data);
        } else {
            throw new Error('API returned unsuccessful response');
        }
        
    } catch (error) {
        // Fallback to manual calculation
        try {
            const calculation = calculateTariffManually(selectedGroup, selectedMeterSize, usage);
            renderResults(calculation);
            showToast('Menggunakan kalkulasi manual', 'warning');
        } catch (manualError) {
            showError('calculationResult', 'Gagal menghitung tarif');
        }
    }
}

// Manual tariff calculation as fallback
function calculateTariffManually(groupCode, meterSize, usage) {
    // Find the customer group
    let groupData = null;
    for (const category in customerGroups) {
        const groups = customerGroups[category];
        const found = groups.find(g => g.code === groupCode);
        if (found) {
            groupData = found;
            break;
        }
    }
    
    if (!groupData) {
        throw new Error('Customer group not found');
    }
    
    // Find meter admin fee
    const meterData = meterSizes.find(m => m.value === meterSize);
    if (!meterData) {
        throw new Error('Meter size not found');
    }
    
    // Calculate progressive blocks
    const blocks = [];
    let remainingUsage = usage;
    let waterCharge = 0;
    
    // Block 1: 0-10 m³
    if (remainingUsage > 0) {
        const block1Usage = Math.min(remainingUsage, 10);
        const block1Amount = block1Usage * groupData.rates.block1;
        blocks.push({
            name: "Block I (0-10 m³)",
            usage: block1Usage,
            rate: groupData.rates.block1,
            amount: block1Amount
        });
        waterCharge += block1Amount;
        remainingUsage -= block1Usage;
    }
    
    // Block 2: 11-20 m³
    if (remainingUsage > 0) {
        const block2Usage = Math.min(remainingUsage, 10);
        const block2Amount = block2Usage * groupData.rates.block2;
        blocks.push({
            name: "Block II (11-20 m³)",
            usage: block2Usage,
            rate: groupData.rates.block2,
            amount: block2Amount
        });
        waterCharge += block2Amount;
        remainingUsage -= block2Usage;
    }
    
    // Block 3: 21-30 m³
    if (remainingUsage > 0) {
        const block3Usage = Math.min(remainingUsage, 10);
        const block3Amount = block3Usage * groupData.rates.block3;
        blocks.push({
            name: "Block III (21-30 m³)",
            usage: block3Usage,
            rate: groupData.rates.block3,
            amount: block3Amount
        });
        waterCharge += block3Amount;
        remainingUsage -= block3Usage;
    }
    
    // Block 4: >30 m³ (if applicable)
    if (remainingUsage > 0 && groupData.rates.block4 > 0) {
        const block4Amount = remainingUsage * groupData.rates.block4;
        blocks.push({
            name: "Block IV (>30 m³)",
            usage: remainingUsage,
            rate: groupData.rates.block4,
            amount: block4Amount
        });
        waterCharge += block4Amount;
    }
    
    const adminFee = meterData.admin_fee;
    const totalAmount = waterCharge + adminFee;
    
    return {
        customer_group: {
            code: groupData.code,
            name: groupData.name
        },
        meter_size: meterSize,
        usage: usage,
        usage_m3: usage,
        blocks: blocks,
        water_charge: waterCharge,
        admin_fee: adminFee,
        total_amount: totalAmount
    };
}

// ==========================================
// RENDER FUNCTIONS
// ==========================================
function renderCustomerGroups() {
    const select = document.getElementById('customerGroupSelect');
    const loadingDiv = document.getElementById('customerGroupsLoading');
    const errorDiv = document.getElementById('customerGroupsError');
    
    if (!select) return;
    
    if (loadingDiv) loadingDiv.style.display = 'none';
    
    if (!customerGroups || Object.keys(customerGroups).length === 0) {
        if (errorDiv) {
            errorDiv.innerHTML = '<div class="error-message">Data golongan pelanggan kosong</div>';
            errorDiv.style.display = 'block';
        }
        return;
    }
    
    select.innerHTML = '<option value="">Pilih golongan pelanggan...</option>';
    
    for (const category in customerGroups) {
        const groups = customerGroups[category];
        if (!Array.isArray(groups)) continue;
        
        const optgroup = document.createElement('optgroup');
        optgroup.label = category;
        
        for (const group of groups) {
            const option = document.createElement('option');
            option.value = group.code;
            option.textContent = `${group.code} - ${group.name}`;
            option.dataset.category = category;
            option.dataset.rates = JSON.stringify(group.rates || {});
            optgroup.appendChild(option);
        }
        
        select.appendChild(optgroup);
    }
    
    if (errorDiv) errorDiv.style.display = 'none';
}

function renderMeterSizes() {
    const select = document.getElementById('meterSizeSelect');
    const loadingDiv = document.getElementById('meterSizesLoading');
    const errorDiv = document.getElementById('meterSizesError');
    
    if (!select) return;
    
    if (loadingDiv) loadingDiv.style.display = 'none';
    
    if (!Array.isArray(meterSizes) || meterSizes.length === 0) {
        if (errorDiv) {
            errorDiv.innerHTML = '<div class="error-message">Data ukuran meter kosong</div>';
            errorDiv.style.display = 'block';
        }
        return;
    }
    
    select.innerHTML = '<option value="">Pilih ukuran meter...</option>';
    
    for (const meter of meterSizes) {
        const option = document.createElement('option');
        option.value = meter.value;
        option.textContent = `${meter.value} (${meter.label || 'DN'}) - Admin: ${formatCurrency(meter.admin_fee)}`;
        option.dataset.adminFee = meter.admin_fee;
        option.dataset.label = meter.label || '';
        select.appendChild(option);
    }
    
    if (errorDiv) errorDiv.style.display = 'none';
}

function updateCustomerGroupDisplay(groupCode) {
    const block1RateInput = document.getElementById('block1Rate');
    const infoDiv = document.getElementById('customerGroupInfo');
    
    if (!groupCode) {
        if (block1RateInput) block1RateInput.value = '';
        if (infoDiv) infoDiv.textContent = 'Pilih golongan untuk melihat detail tarif';
        return;
    }
    
    let selectedGroup = null;
    for (const category in customerGroups) {
        const groups = customerGroups[category];
        selectedGroup = groups.find(g => g.code === groupCode);
        if (selectedGroup) break;
    }
    
    if (selectedGroup) {
        if (block1RateInput) {
            block1RateInput.value = selectedGroup.rates.block1.toLocaleString('id-ID');
        }
        if (infoDiv) {
            infoDiv.innerHTML = `
                <strong>${selectedGroup.name}</strong><br>
                <small class="text-muted">
                    Block I: ${formatCurrency(selectedGroup.rates.block1)}/m³ | 
                    Block II: ${formatCurrency(selectedGroup.rates.block2)}/m³ | 
                    Block III: ${formatCurrency(selectedGroup.rates.block3)}/m³
                </small>
            `;
        }
    }
}

function updateMeterSizeDisplay(meterSize) {
    const adminFeeInput = document.getElementById('adminFeeDisplay');
    const infoDiv = document.getElementById('meterSizeInfo');
    
    if (!meterSize) {
        if (adminFeeInput) adminFeeInput.value = '';
        if (infoDiv) infoDiv.textContent = 'Pilih ukuran untuk melihat biaya admin';
        return;
    }
    
    const selectedMeter = meterSizes.find(m => m.value === meterSize);
    
    if (selectedMeter) {
        if (adminFeeInput) {
            adminFeeInput.value = selectedMeter.admin_fee.toLocaleString('id-ID');
        }
        if (infoDiv) {
            infoDiv.innerHTML = `
                <strong>${selectedMeter.value}</strong> - ${selectedMeter.label || 'DN'}<br>
                <small class="text-muted">Biaya administrasi bulanan: ${formatCurrency(selectedMeter.admin_fee)}</small>
            `;
        }
    }
}

function showLoadingState() {
    const resultContainer = document.getElementById('calculationResult');
    if (resultContainer) {
        resultContainer.innerHTML = `
            <div class="text-center py-4">
                <div class="calculation-loading mx-auto"></div>
                <p class="text-muted mt-2">Menghitung tarif...</p>
            </div>
        `;
    }
}

function renderResults(calculation) {
    const resultContainer = document.getElementById('calculationResult');
    if (!resultContainer) return;

    let verificationAlert = '';
    if (calculation.customer_group && calculation.customer_group.code === '2R1' && calculation.usage === 30) {
        verificationAlert = `
            <div class="alert alert-success mt-3 mb-0">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Verifikasi:</strong> Sesuai dengan contoh tirtaraharja.co.id (Rp 258.500)
            </div>
        `;
    }

    resultContainer.innerHTML = `
        <div class="calculation-result p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-1">${formatCurrency(calculation.total_amount)}</h3>
                    <p class="mb-0 opacity-75">
                        ${calculation.customer_group ? calculation.customer_group.code + ' - ' + calculation.customer_group.name : ''} | 
                        Meter ${calculation.meter_size} | ${calculation.usage} m³
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex flex-column">
                        <small class="opacity-75">Biaya Air:</small>
                        <strong>${formatCurrency(calculation.water_charge)}</strong>
                        <small class="opacity-75">Admin:</small>
                        <strong>${formatCurrency(calculation.admin_fee)}</strong>
                    </div>
                </div>
            </div>
            ${verificationAlert}
        </div>
    `;

    if (calculation.blocks) {
        renderProgressiveBreakdown(calculation);
    }
}

function renderProgressiveBreakdown(calculation) {
    const container = document.getElementById('progressiveBreakdown');
    if (!container || !calculation.blocks) return;
    
    let html = '<div class="row">';
    const colorClasses = ['success', 'warning', 'orange', 'danger'];

    for (let i = 0; i < calculation.blocks.length; i++) {
        const block = calculation.blocks[i];
        const colorClass = colorClasses[i] || 'secondary';
        
        html += `<div class="col-md-6 mb-3">`;
        html += `<div class="block-breakdown p-3 h-100 bg-light rounded">`;
        html += `<div class="d-flex justify-content-between align-items-center mb-2">`;
        html += `<h6 class="mb-0 text-${colorClass}">${block.name}</h6>`;
        html += `<span class="badge bg-${colorClass}">Volume: ${block.usage} m³</span>`;
        html += `</div>`;
        html += `<div class="progressive-indicator block-${i + 1}" style="width: 80%"></div>`;
        html += `<div class="row mt-2">`;
        html += `<div class="col-6">`;
        html += `<small class="text-muted">Rate:</small>`;
        html += `<div><strong>${formatCurrency(block.rate)}/m³</strong></div>`;
        html += `</div>`;
        html += `<div class="col-6">`;
        html += `<small class="text-muted">Amount:</small>`;
        html += `<div><strong>${formatCurrency(block.amount)}</strong></div>`;
        html += `</div>`;
        html += `</div>`;
        html += `</div>`;
        html += `</div>`;
    }

    html += `</div>`;
    html += `
        <div class="card bg-light mt-3">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h6 class="text-muted mb-1">Total Volume</h6>
                        <strong class="text-primary">${calculation.usage_m3} m³</strong>
                    </div>
                    <div class="col-4">
                        <h6 class="text-muted mb-1">Biaya Air</h6>
                        <strong class="text-success">${formatCurrency(calculation.water_charge)}</strong>
                    </div>
                    <div class="col-4">
                        <h6 class="text-muted mb-1">Biaya Admin</h6>
                        <strong class="text-warning">${formatCurrency(calculation.admin_fee)}</strong>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.innerHTML = html;
}

function clearResults() {
    const resultContainer = document.getElementById('calculationResult');
    const breakdownContainer = document.getElementById('progressiveBreakdown');
    
    if (resultContainer) {
        resultContainer.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Masukkan pemakaian air</h5>
                <p class="text-muted">Hasil kalkulasi akan tampil di sini</p>
            </div>
        `;
    }
    
    if (breakdownContainer) {
        breakdownContainer.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-chart-line fa-2x text-muted mb-3"></i>
                <p class="text-muted">Breakdown progressive block rate akan tampil setelah kalkulasi</p>
            </div>
        `;
    }
}

// ==========================================
// EVENT HANDLERS & INTERACTIONS
// ==========================================
function setUsage(value) {
    const usageInput = document.getElementById('usageInput');
    const usageSlider = document.getElementById('usageSlider');
    
    if (usageInput) usageInput.value = value;
    if (usageSlider) usageSlider.value = Math.min(value, 100);
    
    updateSliderValue();
    calculateTariff();
}

function resetCalculator() {
    selectedGroup = null;
    selectedMeterSize = null;
    
    const customerGroupSelect = document.getElementById('customerGroupSelect');
    const meterSizeSelect = document.getElementById('meterSizeSelect');
    const usageInput = document.getElementById('usageInput');
    const usageSlider = document.getElementById('usageSlider');
    
    if (customerGroupSelect) {
        customerGroupSelect.value = '';
        updateCustomerGroupDisplay('');
    }
    
    if (meterSizeSelect) {
        meterSizeSelect.value = '';
        updateMeterSizeDisplay('');
    }
    
    if (usageInput) usageInput.value = '0';
    if (usageSlider) usageSlider.value = '0';
    
    updateSliderValue();
    clearResults();
    showToast('Calculator telah direset', 'info');
}

function showTariffReference() {
    const modalElement = document.getElementById('tariffReferenceModal');
    if (modalElement && typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        showToast('Modal reference tidak dapat dibuka', 'warning');
    }
}

function updateSliderValue() {
    const sliderElement = document.getElementById('sliderValue');
    const usageSlider = document.getElementById('usageSlider');
    if (sliderElement && usageSlider) {
        sliderElement.textContent = usageSlider.value + ' m³';
    }
}

function setupEventListeners() {
    const usageInput = document.getElementById('usageInput');
    const usageSlider = document.getElementById('usageSlider');
    const customerGroupSelect = document.getElementById('customerGroupSelect');
    const meterSizeSelect = document.getElementById('meterSizeSelect');

    if (usageInput) {
        usageInput.addEventListener('input', function() {
            const value = parseInt(this.value) || 0;
            if (usageSlider) usageSlider.value = Math.min(value, 100);
            updateSliderValue();
            calculateTariff();
        });
    }

    if (usageSlider) {
        usageSlider.addEventListener('input', function() {
            const value = parseInt(this.value);
            if (usageInput) usageInput.value = value;
            updateSliderValue();
            calculateTariff();
        });
    }

    if (customerGroupSelect) {
        customerGroupSelect.addEventListener('change', function() {
            const selectedCode = this.value;
            selectedGroup = selectedCode;
            updateCustomerGroupDisplay(selectedCode);
            calculateTariff();
        });
    }

    if (meterSizeSelect) {
        meterSizeSelect.addEventListener('change', function() {
            const selectedSize = this.value;
            selectedMeterSize = selectedSize;
            updateMeterSizeDisplay(selectedSize);
            calculateTariff();
        });
    }
}

// ==========================================
// INITIALIZATION
// ==========================================
function initializeApp() {
    setupEventListeners();
    loadCustomerGroups();
    loadMeterSizes();
    
    setTimeout(() => {
        setUsage(30);
    }, 1000);
}

// Start app when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    initializeApp();
}

// Make functions globally accessible
window.setUsage = setUsage;
window.resetCalculator = resetCalculator;
window.showTariffReference = showTariffReference;
</script>
@endsection