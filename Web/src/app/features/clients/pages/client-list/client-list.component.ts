import { Component, OnInit, inject, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MatTableDataSource } from '@angular/material/table';
import { Client } from '../../models/client.model';
import { ClientService } from '../../services/client.service';
import { MaterialModule } from '../../../../shared/material.module';
import { catchError, timeout, of, finalize } from 'rxjs';
import { NotificationService } from '../../../../shared/services/notification.service';

@Component({
    selector: 'app-client-list',
    standalone: true,
    imports: [CommonModule, MaterialModule, RouterModule],
    templateUrl: './client-list.component.html',
    styleUrls: ['./client-list.component.scss']
})
export class ClientListComponent implements OnInit {
    private clientService = inject(ClientService);
    private notificationService = inject(NotificationService);
    private cdr = inject(ChangeDetectorRef);

    displayedColumns: string[] = ['name', 'last_name', 'email', 'phone', 'address', 'actions'];
    dataSource = new MatTableDataSource<Client>([]);
    isLoading = true;
    errorMessage = '';
    hasError = false;

    ngOnInit() {
        this.loadClients();
    }

    loadClients() {
        this.isLoading = true;
        this.hasError = false;
        this.errorMessage = '';

        this.clientService.getClients()
            .pipe(
                timeout(10000), // 10 second timeout
                catchError((err) => {
                    console.error('Error loading clients', err);
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
                        this.errorMessage = err.error?.message || 'Failed to load clients. Please try again.';
                    }

                    return of([]); // Return empty array on error
                }),
                finalize(() => {
                    console.log('[ClientList] finalize called, stopping loading');
                    this.isLoading = false;
                    // Force change detection in case it didn't run automatically
                    this.cdr.detectChanges();
                })
            )
            .subscribe({
                next: (data) => {
                    console.log('[ClientList] data received', data);
                    this.dataSource.data = data;
                },
                error: () => {
                    // This shouldn't happen due to catchError, but just in case
                }
            });
    }

    async deleteClient(id: number) {
        const confirmed = await this.notificationService.confirm(
            'Are you sure you want to delete this client?',
            'Delete'
        );

        if (confirmed) {
            this.clientService.deleteClient(id).subscribe({
                next: () => {
                    this.notificationService.success('Client deleted successfully');
                    this.loadClients();
                },
                error: (err) => {
                    console.error('Error deleting client', err);
                    this.notificationService.error('Failed to delete client. Please try again.');
                }
            });
        }
    }
}
