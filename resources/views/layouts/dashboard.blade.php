@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <div class="welcome-text">
            <h2>Welcome back, Valeria!</h2>
            <p class="text-muted">It is the best time to manage your finances</p>
        </div>
    </div>
    <div class="col-md-4 text-right">
        <button class="btn btn-outline-secondary">
            <i class="fas fa-th"></i> Manage widgets
        </button>
        <button class="btn btn-primary ml-2">
            <i class="fas fa-plus"></i> Add new widget
        </button>
    </div>
</div>

<!-- Dashboard Cards -->
@include('partials.dashboard-cards')

<!-- Dashboard Charts -->
<div class="row">
    <div class="col-md-6">
        <!-- Money Flow Chart -->
        @include('partials.charts.money-flow')
    </div>
    <div class="col-md-6">
        <!-- Budget Chart -->
        @include('partials.charts.budget-chart')
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    // Money Flow Chart
    var moneyFlowCtx = document.getElementById('money-flow-chart').getContext('2d');
    var moneyFlowChart = new Chart(moneyFlowCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Income',
                data: [7500, 8200, 7800, 8100, 8500, 8700],
                borderColor: '#6c5ce7',
                backgroundColor: 'rgba(108, 92, 231, 0.1)',
                tension: 0.4,
                borderWidth: 2,
                fill: true
            },
            {
                label: 'Expenses',
                data: [6000, 6100, 5900, 6300, 6222, 6500],
                borderColor: '#a29bfe',
                backgroundColor: 'rgba(162, 155, 254, 0.1)',
                tension: 0.4,
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    
    // Budget Chart
    var budgetCtx = document.getElementById('budget-chart').getContext('2d');
    var budgetChart = new Chart(budgetCtx, {
        type: 'doughnut',
        data: {
            labels: ['Housing', 'Food', 'Transport', 'Entertainment', 'Others'],
            datasets: [{
                data: [35, 20, 15, 10, 20],
                backgroundColor: [
                    '#6c5ce7',
                    '#a29bfe', 
                    '#74b9ff',
                    '#ff7675',
                    '#fdcb6e'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endsection