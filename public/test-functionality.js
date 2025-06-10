// Test funkcji panelu kierowcy
console.log('🧪 Rozpoczynam test funkcjonalności...');

// 1. Test ładowania panelu kierowcy
async function testDriverPanel() {
    console.log('📱 Test 1: Ładowanie panelu kierowcy');
    
    try {
        const response = await fetch('./views/driverPanel.php');
        const html = await response.text();
        
        if (html.includes('modal-dialog')) {
            console.log('✅ Panel kierowcy ładuje się poprawnie');
        } else {
            console.log('❌ Panel kierowcy ma problemy');
        }
        
        // Sprawdź data-action atrybuty
        const dataActionCount = (html.match(/data-action="/g) || []).length;
        console.log(`📊 Znaleziono ${dataActionCount} elementów z data-action`);
        
    } catch (error) {
        console.error('❌ Błąd:', error.message);
    }
}

// 2. Test event listenerów
function testEventListeners() {
    console.log('🎯 Test 2: Event listenery');
    
    // Symuluj przyciski z data-action
    const testButton = document.createElement('button');
    testButton.setAttribute('data-action', 'test-action');
    testButton.textContent = 'Test Button';
    document.body.appendChild(testButton);
    
    testButton.addEventListener('click', (e) => {
        console.log('✅ Event listener działa!', e.target.getAttribute('data-action'));
    });
    
    testButton.click();
    testButton.remove();
}

// 3. Test funkcji API
async function testAPI() {
    console.log('🌐 Test 3: API');
    
    try {
        const response = await fetch('/api/queries/orders');
        if (response.ok) {
            const data = await response.json();
            console.log('✅ API działa, znaleziono', data.length, 'zleceń');
        } else {
            console.log('⚠️ API zwraca błąd:', response.status);
        }
    } catch (error) {
        console.log('❌ Błąd API:', error.message);
    }
}

// Uruchom wszystkie testy
async function runAllTests() {
    await testDriverPanel();
    testEventListeners();
    await testAPI();
    console.log('🏁 Testy zakończone!');
}

// Auto-start
runAllTests();
