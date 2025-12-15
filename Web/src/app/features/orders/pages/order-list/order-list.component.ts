import { Component, OnInit, inject, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormBuilder, ReactiveFormsModule } from '@angular/forms';
import { MatTableDataSource } from '@angular/material/table';
import { Order, OrderFilters } from '../../models/order.model';
import { OrderService } from '../../services/order.service';
import { MaterialModule } from '../../../../shared/material.module';
import { ClientService } from '../../../clients/services/client.service';
import { Client } from '../../../clients/models/client.model';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule } from '@angular/material/core';
import { catchError, timeout, of, finalize } from 'rxjs';
import { NotificationService } from '../../../../shared/services/notification.service';

@Component({
    selector: 'app-order-list',
    standalone: true,
    imports: [CommonModule, MaterialModule, RouterModule, ReactiveFormsModule, MatDatepickerModule, MatNativeDateModule],
    templateUrl: './order-list.component.html',
    styleUrls: ['./order-list.component.scss']
})
export class OrderListComponent implements OnInit {
    private orderService = inject(OrderService);
    private clientService = inject(ClientService);
    private fb = inject(FormBuilder);
    private notificationService = inject(NotificationService);
    private cdr = inject(ChangeDetectorRef);

    displayedColumns: string[] = ['id', 'client', 'description', 'amount', 'status', 'created_at', 'actions'];
    dataSource = new MatTableDataSource<Order>([]);
    isLoading = true;
    errorMessage = '';
    hasError = false;
    clients: Client[] = [];

    filterForm = this.fb.group({
        status: [''],
        client_id: [''],
        date_from: ['']
    });

    ngOnInit() {
        this.loadClients();
        this.loadOrders();

        // Auto-reload on filter change if desired, or use a button
        // this.filterForm.valueChanges.subscribe(() => this.loadOrders());
    }

    loadClients() {
        this.clientService.getClients()
            .pipe(
                timeout(10000),
                catchError((err) => {
                    console.error('Error loading clients for filter', err);
                    return of([]);
                })
            )
            .subscribe(clients => {
                this.clients = clients;
            });
    }

    loadOrders() {
        this.isLoading = true;
        this.hasError = false;
        this.errorMessage = '';

        const filters = this.filterForm.value;

        // Format date if present
        let dateStr = '';
        if (filters.date_from) {
            const date = new Date(filters.date_from);
            dateStr = date.toISOString().split('T')[0];
        }

        const apiFilters: OrderFilters = {
            status: filters.status || undefined,
            client_id: filters.client_id ? +filters.client_id : undefined,
            date_from: dateStr || undefined
        };

        this.orderService.getOrders(apiFilters)
            .pipe(
                timeout(10000), // 10 second timeout
                catchError((err) => {
                    console.error('Error loading orders', err);
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
                        this.errorMessage = err.error?.message || 'Failed to load orders. Please try again.';
                    }

                    return of([]); // Return empty array on error
                }),
                finalize(() => {
                    console.log('[OrderList] finalize called, stopping loading');
                    this.isLoading = false;
                    // Force change detection as in ClientList
                    this.cdr.detectChanges();
                })
            )
            .subscribe({
                next: (data) => {
                    console.log('[OrderList] data received', data);
                    this.dataSource.data = data;
                },
                error: () => {
                    // This shouldn't happen due to catchError, but just in case
                }
            });
    }

    applyFilters() {
        this.loadOrders();
    }

    clearFilters() {
        this.filterForm.reset();
        this.loadOrders();
    }

    updateStatus(order: Order, status: 'pending' | 'completed' | 'cancelled') {
        if (order.id) {
            this.orderService.updateStatus(order.id, status).subscribe({
                next: () => {
                    this.notificationService.success(`Order status updated to ${status}`);
                    this.loadOrders(); // Reload to reflect changes
                },
                error: (err) => {
                    console.error('Error updating order status', err);
                    this.notificationService.error('Failed to update order status. Please try again.');
                }
            });
        }
    }

    async deleteOrder(id: number) {
        const confirmed = await this.notificationService.confirm(
            'Are you sure you want to delete this order?',
            'Delete'
        );

        if (confirmed) {
            this.orderService.deleteOrder(id).subscribe({
                next: () => {
                    this.notificationService.success('Order deleted successfully');
                    this.loadOrders();
                },
                error: (err) => {
                    console.error('Error deleting order', err);
                    this.notificationService.error('Failed to delete order. Please try again.');
                }
            });
        }
    }

    getClientName(clientId: number): string {
        const client = this.clients.find(c => c.id === clientId);
        return client ? `${client.name} ${client.last_name}` : `Client #${clientId}`;
    }
}
