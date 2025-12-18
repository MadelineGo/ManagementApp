import { Component, OnInit, inject, ElementRef, ViewChild, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { DashboardService } from '../../services/dashboard.service';
import { DashboardStats } from '../../models/dashboard.model';
import Chart from 'chart.js/auto';
import { catchError, timeout, of } from 'rxjs';

@Component({
    selector: 'app-dashboard-stats',
    standalone: true,
    imports: [CommonModule, MatCardModule, MatIconModule, MatButtonModule],
    templateUrl: './dashboard.component.html',
    styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit {
    private dashboardService = inject(DashboardService);

    constructor(private cdr: ChangeDetectorRef) { }

    stats: DashboardStats | null = null;
    isLoading = true;
    hasError = false;
    errorMessage: string | null = null;
    monthlyChart: any;
    statusChart: any;
    readonly dashboardColors = {
        primary: '#3f51b5',   // orders / primary
        success: '#4caf50',   // completed
        warn: '#ff9800',      // pending
        info: '#2196f3',      // clients
    };
    //private activityChartRef?: ElementRef;
    //private activityChartRef?: ElementRef<HTMLCanvasElement>;
    /**
        ngAfterViewInit(): void {
            requestAnimationFrame(() => this.tryInitMonthlyChart());
        }
    */
    @ViewChild('activityChart') activityChartRef?: ElementRef<HTMLCanvasElement>;
    @ViewChild('statusChart') statusChartRef?: ElementRef<HTMLCanvasElement>;
    /**
     
     @ViewChild('activityChart')
        set activityChart(ref: ElementRef<HTMLCanvasElement> | undefined) {
            this.activityChartRef = ref;
            if (ref) {
                // Once the canvas exists in the DOM, try to draw the chart
                this.initMonthlyChart();
            }
        }
     */


    private dataReady = false;

    ngOnInit() {
        this.loadStats();
    }

    ngOnDestroy() {
        this.monthlyChart?.destroy();
        this.statusChart?.destroy();
    }

    loadStats() {
        this.isLoading = true;
        this.hasError = false;
        this.errorMessage = null;

        this.dashboardService.getStats()
            .pipe(
                //timeout(10000), // 10 second timeout
                catchError((err) => {
                    console.error('Error loading stats', err);
                    this.hasError = true;

                    if (err.name === 'TimeoutError') {
                        this.errorMessage = 'Request timeout. Please check your connection and try again.';
                    } else if (err.status === 0) {
                        this.errorMessage = 'Unable to connect to server. Please ensure the backend service is running.';
                    } else if (err.status === 401) {
                        this.errorMessage = 'Unauthorized. Please login again.';
                    } else if (err.status >= 500) {
                        this.errorMessage = 'Server error. Please try again later.';
                    } else {
                        this.errorMessage = 'Failed to load dashboard statistics. ' + (err.error?.message || err.message || 'Unknown error');
                    }

                    this.isLoading = false;
                    return of(null as DashboardStats | null);
                })
            )
            .subscribe({
                next: (data) => {
                    console.log('[Dashboard] data received', data);
                    // If backend returns null or an empty array, treat as "no stats"
                    if (!data || Array.isArray(data)) {
                        this.stats = null;
                        this.isLoading = false;
                        return;
                    }

                    this.stats = data;
                    this.hasError = false;
                    this.isLoading = false;

                    this.cdr.detectChanges();
                    this.dataReady = true;
                    requestAnimationFrame(() => {
                        this.initMonthlyChart();
                        this.initStatusChart();
                    });

                    //setTimeout(() => this.initMonthlyChart());
                },
                error: () => {
                    // This shouldn't happen due to catchError, but just in case
                    this.isLoading = false;
                }
            });
    }

    tryInitMonthlyChart() {
        console.log('tryInitMonthlyChart', {
            dataReady: this.dataReady,
            canvas: !!this.activityChartRef,
            monthly: this.stats?.monthly_activity?.length
        });

        if (!this.dataReady) return;
        if (!this.activityChartRef) return;
        if (!this.stats?.monthly_activity?.length) return;

        this.initMonthlyChart();
    }

    private tryInitStatusChart() {
        if (!this.dataReady) return;
        if (!this.statusChartRef) return;
        if (!this.stats?.orders_by_status) return;

        this.initStatusChart();
    }

    private formatMonthLabel(ym: string): string {
        const [y, m] = ym.split('-').map(Number);
        const date = new Date(y, m - 1, 1);

        let s = new Intl.DateTimeFormat('es-EC', { month: 'short', year: 'numeric' }).format(date);
        s = s.replace('.', '');                 // quita punto si existe
        return s.charAt(0).toUpperCase() + s.slice(1); // capitaliza
    }

    initMonthlyChart() {
        console.log('Initializing monthly chart...', this.stats, this.activityChartRef);

        // Only init when both the data and the canvas are ready
        if (!this.stats?.monthly_activity || !this.activityChartRef || !this.stats.monthly_activity.length) {
            console.warn('Cannot init monthly chart: missing data or canvas', {
                data: !!this.stats?.monthly_activity,
                canvas: !!this.activityChartRef
            });
            return;
        }

        if (this.monthlyChart) {
            this.monthlyChart.destroy();
        }

        const ctx = this.activityChartRef.nativeElement.getContext('2d');
        if (!ctx) return;
        console.log('Canvas context found, creating chart...');

        this.monthlyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: this.stats.monthly_activity.map(d => this.formatMonthLabel(d.month)),
                datasets: [{
                    label: 'Orders',
                    data: this.stats.monthly_activity.map(d => d.count),
                    backgroundColor: this.dashboardColors.primary + '80', // 50% alpha
                    borderColor: this.dashboardColors.primary,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Activity'
                    }
                }
            }
        });
    }

    private initStatusChart() {
        const ctx = this.statusChartRef!.nativeElement.getContext('2d');
        if (!ctx || !this.stats) return;

        this.statusChart?.destroy();

        const completed = this.stats.orders_by_status?.completed ?? 0;
        const pending = this.stats.orders_by_status?.pending ?? 0;

        this.statusChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Completed', 'Pending'],
                datasets: [{
                    label: 'Orders',
                    data: [completed, pending],
                    backgroundColor: [
                        this.dashboardColors.success,
                        this.dashboardColors.warn
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Orders by Status' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }


}
