@extends('layouts.app')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            {{-- Total Income Card --}}
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="ibox-tools">
                            <span class="label label-success float-right">Total</span>
                        </div>
                        <h5>Pendapatan</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h1>
                        <div class="stat-percent text-success font-bold">
                            {{ $incomeGrowth }}% <i class="fa {{ $incomeGrowth >= 0 ? 'fa-bolt' : 'fa-level-down' }}"></i>
                        </div>
                        <small>Total pendapatan</small>
                    </div>
                </div>
            </div>

            {{-- Pending Transactions Card --}}
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="ibox-tools">
                            <span class="label label-warning float-right">Pending</span>
                        </div>
                        <h5>Transaksi Pending</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">Rp {{ number_format($pendingTransactions, 0, ',', '.') }}</h1>
                        <div class="stat-percent text-info font-bold">
                            20% <i class="fa fa-level-up"></i>
                        </div>
                        <small>Transaksi Pending</small>
                    </div>
                </div>
            </div>

            {{-- Total Users Card --}}
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="ibox-tools">
                            <span class="label label-primary float-right">Total</span>
                        </div>
                        <h5>Jumlah User</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{ number_format($totalUsers, 0, ',', '.') }}</h1>
                        <div class="stat-percent text-navy font-bold">
                            {{ $userGrowth }}% <i class="fa {{ $userGrowth >= 0 ? 'fa-level-up' : 'fa-level-down' }}"></i>
                        </div>
                        <small>Total user</small>
                    </div>
                </div>
            </div>

            {{-- Active Subscribers Card --}}
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="ibox-tools">
                            <span class="label label-danger float-right">Aktif</span>
                        </div>
                        <h5>User Berlangganan</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{ number_format($activeSubscribers, 0, ',', '.') }}</h1>
                        <div class="stat-percent text-danger font-bold">
                            {{ round(($activeSubscribers / max($totalUsers, 1)) * 100) }}% <i class="fa fa-level-up"></i>
                        </div>
                        <small>User berlangganan</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Orders</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-9">
                                <div class="flot-chart">
                                    <div class="flot-chart-content" id="flot-dashboard-chart"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <ul class="stat-list">
                                    <li>
                                        <h2 class="no-margins">{{ number_format($totalOrders) }}</h2>
                                        <small>Total transaksi selesai</small>
                                        <div class="stat-percent">{{ $totalOrdersPercent }}%
                                            <i
                                                class="fa fa-level-{{ $totalOrdersPercent >= 50 ? 'up' : 'down' }} text-navy"></i>
                                        </div>
                                        <div class="progress progress-mini">
                                            <div style="width: {{ $totalOrdersPercent }}%;" class="progress-bar"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <h2 class="no-margins">{{ number_format($pendingOrders) }}</h2>
                                        <small>Transaksi pending</small>
                                        <div class="stat-percent">{{ $pendingOrdersPercent }}%
                                            <i
                                                class="fa fa-level-{{ $pendingOrdersPercent < 50 ? 'up' : 'down' }} text-navy"></i>
                                        </div>
                                        <div class="progress progress-mini">
                                            <div style="width: {{ $pendingOrdersPercent }}%;" class="progress-bar"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <h2 class="no-margins">Rp {{ number_format($currentMonthIncome) }}</h2>
                                        <small>Pendapatan bulanan</small>
                                        <div class="stat-percent">{{ $monthlyIncomeGrowth }}%
                                            <i
                                                class="fa fa-{{ $monthlyIncomeGrowth > 0 ? 'bolt' : 'level-down' }} text-navy"></i>
                                        </div>
                                        <div class="progress progress-mini">
                                            <div style="width: {{ min(abs($monthlyIncomeGrowth), 100) }}%;"
                                                class="progress-bar"></div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

    <!-- Flot -->
    <script src="{{ asset('js/plugins/flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.spline.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.pie.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.symbol.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.time.js') }}"></script>

    <!-- Peity -->
    <script src="{{ asset('js/plugins/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ asset('js/demo/peity-demo.js') }}"></script>

    <!-- Custom and plugin javascript -->
    <script src="{{ asset('js/inspinia.js') }}"></script>
    <script src="{{ asset('js/plugins/pace/pace.min.js') }}"></script>

    <!-- jQuery UI -->
    <script src="{{ asset('js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

    <!-- Jvectormap -->
    <script src="{{ asset('js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>

    <!-- EayPIE -->
    <script src="{{ asset('js/plugins/easypiechart/jquery.easypiechart.js') }}"></script>

    <!-- Sparkline -->
    <script src="{{ asset('js/plugins/sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- Sparkline demo data  -->
    <script src="{{ asset('js/demo/sparkline-demo.js') }}"></script>

    <script>
        $(document).ready(function() {
            $(".chart").easyPieChart({
                barColor: "#f8ac59",
                //                scaleColor: false,
                scaleLength: 5,
                lineWidth: 4,
                size: 80,
            });

            $(".chart2").easyPieChart({
                barColor: "#1c84c6",
                //                scaleColor: false,
                scaleLength: 5,
                lineWidth: 4,
                size: 80,
            });

            var chartData = @json($chartData);

            var dataset = [{
                    label: "Number of orders",
                    data: chartData.orders,
                    color: "#1ab394",
                    bars: {
                        show: true,
                        align: "center",
                        barWidth: 24 * 60 * 60 * 600,
                        lineWidth: 0
                    }
                },
                {
                    label: "Payments",
                    data: chartData.payments,
                    yaxis: 2,
                    color: "#1C84C6",
                    lines: {
                        lineWidth: 1,
                        show: true,
                        fill: true,
                        fillColor: {
                            colors: [{
                                    opacity: 0.2
                                },
                                {
                                    opacity: 0.4
                                }
                            ]
                        }
                    },
                    splines: {
                        show: false,
                        tension: 0.6,
                        lineWidth: 1,
                        fill: 0.1
                    }
                }
            ];

            var options = {
                xaxis: {
                    mode: "time",
                    tickSize: [3, "day"],
                    tickLength: 0,
                    axisLabel: "Date",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: "Arial",
                    axisLabelPadding: 10,
                    color: "#d5d5d5"
                },
                yaxes: [{
                        position: "left",
                        color: "#d5d5d5",
                        axisLabelUseCanvas: true,
                        axisLabelFontSizePixels: 12,
                        axisLabelFontFamily: "Arial",
                        axisLabelPadding: 3
                    },
                    {
                        position: "right",
                        clolor: "#d5d5d5",
                        axisLabelUseCanvas: true,
                        axisLabelFontSizePixels: 12,
                        axisLabelFontFamily: " Arial",
                        axisLabelPadding: 67
                    }
                ],
                legend: {
                    noColumns: 1,
                    labelBoxBorderColor: "#000000",
                    position: "nw"
                },
                grid: {
                    hoverable: false,
                    borderWidth: 0
                }
            };

            $.plot($("#flot-dashboard-chart"), dataset, options);
        });
    </script>
@endpush
