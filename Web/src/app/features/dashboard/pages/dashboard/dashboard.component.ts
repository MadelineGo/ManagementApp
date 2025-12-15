import { Component, OnInit, inject, ElementRef, ViewChild } from '@angular/core';
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

    stats: DashboardStats | null = null;
    isLoading = true;
    hasError = false;
    errorMessage: string | null = null;
    chart: any;
    private activityChartRef?: ElementRef;

    @ViewChild('activityChart')
    set activityChart(ref: ElementRef | undefined) {
        this.activityChartRef = ref;
        if (ref) {
            // Once the canvas exists in the DOM, try to draw the chart
            this.initChart();
        }
    }

    ngOnInit() {
        this.loadStats();
    }

    loadStats() {
        this.isLoading = true;
        this.hasError = false;
        this.errorMessage = null;

        this.dashboardService.getStats()
            .pipe(
                timeout(10000), // 10 second timeout
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
                },
                error: () => {
                    // This shouldn't happen due to catchError, but just in case
                    this.isLoading = false;
                }
            });
    }

    initChart() {
        console.log('Initializing chart...', this.stats, this.activityChartRef);

        // Only init when both the data and the canvas are ready
        if (!this.stats?.monthly_activity || !this.activityChartRef || !this.stats.monthly_activity.length) {
            console.warn('Cannot init chart: missing data or canvas', {
                data: !!this.stats?.monthly_activity,
                canvas: !!this.activityChartRef
            });
            return;
        }

        if (this.chart) {
            this.chart.destroy();
        }

        const ctx = this.activityChartRef.nativeElement.getContext('2d');
        console.log('Canvas context found, creating chart...');

        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: this.stats.monthly_activity.map(d => d.month),
                datasets: [{
                    label: 'Orders',
                    data: this.stats.monthly_activity.map(d => d.count),
                    backgroundColor: 'rgba(63, 81, 181, 0.5)',
                    borderColor: 'rgba(63, 81, 181, 1)',
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
}
