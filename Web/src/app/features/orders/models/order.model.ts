export interface Order {
    id?: number;
    client_id: number;
    client_name?: string; // Optional for display if API returns it
    description: string;
    amount: number;
    status: 'pending' | 'completed' | 'cancelled';
    created_at?: string;
}

export interface OrderFilters {
    status?: string;
    client_id?: number;
    date_from?: string;
}
