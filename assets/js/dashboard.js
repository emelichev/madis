function doughnutChart(id, labels, data, color) {
    new Chart($('#' + id), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: color,
                hoverBackgroundColor: color
            }],
        },
        options: {
            legend: {
                display: false,
            },
            tooltips: {
                displayColors: false,
            },
            responsive: true,
            scales: {
                label: {
                    display: false,
                }
            }
        }
    });
}

function radarChart(id, labels, serieLabel, data, color) {
    let dataset = [];
    data.forEach(function(item, index) {
        dataset.push(
            {
                label: serieLabel[index],
                data: item,
                backgroundColor: color[index],
            }
        );
    });

    new Chart($('#' + id), {
        type: 'radar',
        data: {
            labels: labels,
            datasets: dataset,
        },
        options: {
            scale: {
                ticks: {
                    min: 0,
                    max: 5,
                }
            }
        }
    });
}

function barChart(id, labels, serieLabel, data, color) {
    let dataset = [];
    data.forEach(function(item, index) {
        dataset.push(
            {
                label: serieLabel[index],
                data: item,
                backgroundColor: color[index],
            }
        );
    });

    new Chart($('#' + id), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: dataset,
        },
        options: {
            scales: {
                xAxes: [{
                    ticks: {
                        autoSkip: false
                    }
                }],
                yAxes: [{
                    ticks : {
                        beginAtZero: true,
                        stepSize: 1,
                    },
                }]
            }
        },
    });
}

// LOAD GRAPHS
$(document).ready(function() {
    doughnutChart(
        'contractor-clauses',
        labelYesNo,
        contractorClausesData,
        [colorBlue, colorRed]
    );

    doughnutChart(
        'contractor-adopted-security-features',
        labelYesNo,
        contractorAdoptedSecurityFeaturesData,
        [colorBlue, colorRed]
    );

    doughnutChart(
        'contractor-maintains-treatment-register',
        labelYesNo,
        contractorMaintainsTreatmentRegisterData,
        [colorBlue, colorRed]
    );

    doughnutChart(
        'contractor-sending-data-outside-eu',
        labelYesNo,
        contractorSendingDataOutsideEuData,
        [colorBlue, colorRed]
    );

    doughnutChart(
        'request-type',
        requestTypeLabel,
        requestTypeData,
        [colorBlue, colorRed, colorGreen, colorOrange, colorPurple, colorTeal]
    );

    doughnutChart(
        'request-status',
        requestStatusLabel,
        requestStatusData,
        [colorBlue, colorRed, colorGreen]
    );

    radarChart(
        'maturity-radar',
        maturityLabels,
        maturitySerieLabel,
        maturityData,
        [colorBlueOpacity, colorRedOpacity]
    );

    barChart(
        'treatment-bar',
        treatmentLabels,
        ['Conforme', 'Non conforme'],
        [treatmentDatasetYes, treatmentDatasetNo],
        [colorBlueOpacity, colorRedOpacity]
    );
});
