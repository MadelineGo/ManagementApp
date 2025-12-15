import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Order, OrderFilters } from '../models/order.model';

@Injectable({
    providedIn: 'root'
})
export class OrderService {
    private http = inject(HttpClient);
    private apiUrl = '/api/orders';

    getOrders(filters?: OrderFilters): Observable<Order[]> {
        let params = new HttpParams();
        if (filters) {
            if (filters.status) params = params.set('status', filters.status);
            if (filters.client_id) params = params.set('client_id', filters.client_id);
            if (filters.date_from) params = params.set('date_from', filters.date_from);
        }
        return this.http.get<Order[]>(this.apiUrl, { params });
    }

    createOrder(order: Partial<Order>): Observable<Order> {
        return this.http.post<Order>(this.apiUrl, order);
    }

    updateStatus(id: number, status: string): Observable<Order> {
        return this.http.put<Order>(`${this.apiUrl}/${id}`, { status });
    }

    deleteOrder(id: number): Observable<void> {
        return this.http.delete<void>(`${this.apiUrl}/${id}`);
    }
}
