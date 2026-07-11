document.addEventListener('DOMContentLoaded', function () {
    loadApiSummary();
    loadApiRiskRanking();
});

async function loadApiSummary() {
    const apiStatus = document.getElementById('apiStatus');

    try {
        const response = await fetch('/api/summary');
        const result = await response.json();

        if (result.status === true) {
            document.getElementById('apiCountriesCount').textContent = result.data.countries_count;
            document.getElementById('apiPortsCount').textContent = result.data.ports_count;
            document.getElementById('apiHighRiskCount').textContent = result.data.high_risk_count;
            document.getElementById('apiNegativeNewsCount').textContent = result.data.negative_news_count;

            apiStatus.textContent = 'API Connected';
            apiStatus.className = 'risk-badge risk-low';
        }
    } catch (error) {
        apiStatus.textContent = 'API Error';
        apiStatus.className = 'risk-badge risk-high';

        console.error('Gagal mengambil summary API:', error);
    }
}

async function loadApiRiskRanking() {
    const tableBody = document.getElementById('apiRiskTable');

    try {
        const response = await fetch('/api/risk');
        const result = await response.json();

        if (result.status === true) {
            tableBody.innerHTML = '';

            result.data.forEach(function (item) {
                let badgeClass = 'risk-low';

                if (item.total_score >= 60) {
                    badgeClass = 'risk-high';
                } else if (item.total_score >= 35) {
                    badgeClass = 'risk-medium';
                }

                tableBody.innerHTML += `
                    <tr>
                        <td><strong>${item.country_name}</strong></td>
                        <td>${item.weather_score}%</td>
                        <td>${item.inflation_score}%</td>
                        <td>${item.currency_score}%</td>
                        <td>${item.news_score}%</td>
                        <td><strong>${item.total_score}/100</strong></td>
                        <td>
                            <span class="risk-badge ${badgeClass}">
                                ${item.risk_level}
                            </span>
                        </td>
                    </tr>
                `;
            });
        }
    } catch (error) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-danger">
                    Gagal memuat data risiko dari API.
                </td>
            </tr>
        `;

        console.error('Gagal mengambil risk API:', error);
    }
}