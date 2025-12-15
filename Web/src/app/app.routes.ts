import { Routes } from '@angular/router';
import { LoginComponent } from './features/auth/pages/login/login.component';
import { RegisterComponent } from './features/auth/pages/register/register.component';
import { MainLayoutComponent } from './shared/ui/main-layout.component';
import { ClientListComponent } from './features/clients/pages/client-list/client-list.component';
import { ClientFormComponent } from './features/clients/pages/client-form/client-form.component';
import { OrderListComponent } from './features/orders/pages/order-list/order-list.component';
import { OrderFormComponent } from './features/orders/pages/order-form/order-form.component';
import { DashboardComponent } from './features/dashboard/pages/dashboard/dashboard.component';

export const routes: Routes = [
    { path: '', redirectTo: 'auth/login', pathMatch: 'full' },
    { path: 'auth/login', component: LoginComponent },
    { path: 'auth/register', component: RegisterComponent },
    {
        path: 'dashboard',
        component: MainLayoutComponent,
        children: [
            { path: '', redirectTo: 'stats', pathMatch: 'full' },
            { path: 'stats', component: DashboardComponent },
            { path: 'clients', component: ClientListComponent },
            { path: 'clients/new', component: ClientFormComponent },
            { path: 'clients/:id', component: ClientFormComponent },
            { path: 'orders', component: OrderListComponent },
            { path: 'orders/new', component: OrderFormComponent }
        ]
    }
];
