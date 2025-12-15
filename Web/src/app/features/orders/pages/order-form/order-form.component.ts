import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { MaterialModule } from '../../../../shared/material.module';
import { OrderService } from '../../services/order.service';
import { ClientService } from '../../../clients/services/client.service';
import { Client } from '../../../clients/models/client.model';
import { Order } from '../../models/order.model';
import { NotificationService } from '../../../../shared/services/notification.service';

@Component({
    selector: 'app-order-form',
    standalone: true,
    imports: [CommonModule, ReactiveFormsModule, MaterialModule, RouterModule],
    templateUrl: './order-form.component.html',
    styleUrls: ['./order-form.component.scss']
})
export class OrderFormComponent implements OnInit {
    private fb = inject(FormBuilder);
    private orderService = inject(OrderService);
    private clientService = inject(ClientService);
    private router = inject(Router);
    private notificationService = inject(NotificationService);

    orderForm = this.fb.group({
        client_id: ['', Validators.required],
        description: ['', Validators.required],
        amount: ['', [Validators.required, Validators.min(0)]]
    });

    clients: Client[] = [];
    isLoading = false;

    ngOnInit() {
        this.loadClients();
    }

    loadClients() {
        this.clientService.getClients().subscribe(clients => this.clients = clients);
    }

    onSubmit() {
        if (this.orderForm.valid) {
            this.isLoading = true;
            const formValue = this.orderForm.value;

            const newOrder: Partial<Order> = {
                client_id: Number(formValue.client_id), // Ensure it's a number
                description: formValue.description!,
                amount: Number(formValue.amount),
                status: 'pending' // Default status
            };

            this.orderService.createOrder(newOrder).subscribe({
                next: () => {
                    this.isLoading = false;
                    this.notificationService.success('Order created successfully');
                    this.router.navigate(['/dashboard/orders']);
                },
                error: (err) => {
                    console.error('Error creating order', err);
                    this.isLoading = false;
                    this.notificationService.error('Failed to create order');
                }
            });
        }
    }
}
