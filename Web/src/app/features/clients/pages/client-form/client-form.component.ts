import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, ActivatedRoute, RouterModule } from '@angular/router';
import { MaterialModule } from '../../../../shared/material.module';
import { ClientService } from '../../services/client.service';
import { Client } from '../../models/client.model';
import { NotificationService } from '../../../../shared/services/notification.service';

@Component({
    selector: 'app-client-form',
    standalone: true,
    imports: [CommonModule, ReactiveFormsModule, MaterialModule, RouterModule],
    templateUrl: './client-form.component.html',
    styleUrls: ['./client-form.component.scss']
})
export class ClientFormComponent implements OnInit {
    private fb = inject(FormBuilder);
    private clientService = inject(ClientService);
    private router = inject(Router);
    private route = inject(ActivatedRoute);
    private notificationService = inject(NotificationService);

    clientForm: FormGroup = this.fb.group({
        name: ['', Validators.required],
        last_name: [''],
        email: ['', [Validators.required, Validators.email]],
        phone_number: [''],
        address: ['']
    });

    isEditMode = false;
    clientId: number | null = null;
    isLoading = false;

    ngOnInit() {
        this.route.params.subscribe(params => {
            if (params['id']) {
                this.isEditMode = true;
                this.clientId = +params['id'];
                this.loadClient(this.clientId);
            }
        });
    }

    loadClient(id: number) {
        this.isLoading = true;
        this.clientService.getClient(id).subscribe({
            next: (client) => {
                if (client) {
                    this.clientForm.patchValue(client);
                } else {
                    // Client not found (or error in mapped list)
                    this.router.navigate(['/dashboard/clients']);
                }
                this.isLoading = false;
            },
            error: (err) => {
                console.error('Error loading client', err);
                this.isLoading = false;
            }
        });
    }

    onSubmit() {
        if (this.clientForm.valid) {
            this.isLoading = true;
            const clientData: Client = this.clientForm.value;

            if (this.isEditMode && this.clientId) {
                this.clientService.updateClient(this.clientId, clientData).subscribe({
                    next: () => this.handleSuccess(),
                    error: (err) => this.handleError(err)
                });
            } else {
                this.clientService.createClient(clientData).subscribe({
                    next: () => this.handleSuccess(),
                    error: (err) => this.handleError(err)
                });
            }
        }
    }

    handleSuccess() {
        this.isLoading = false;
        this.notificationService.success(this.isEditMode ? 'Client updated successfully' : 'Client created successfully');
        this.router.navigate(['/dashboard/clients']);
    }

    handleError(err: any) {
        this.isLoading = false;
        console.error('Operation Error:', err);
        // Try to extract specific validation message
        let msg = 'Operation failed';
        if (err.error && typeof err.error === 'object') {
            // Check for "Name": ["Error"] format
            const errors = Object.values(err.error).flat();
            if (errors.length > 0) {
                msg = errors.join('\n');
            }
        }
        this.notificationService.error(msg);
    }
}
