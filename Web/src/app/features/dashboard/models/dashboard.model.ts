export interface DashboardStats {
    total_orders: number;
    orders_by_status: {
        completed: number;
        pending: number;
        cancelled: number;
    };
    active_clients: number;
    monthly_activity: ActivityPoint[];
}

export interface ActivityPoint {
    month: string;
    count: number;
}
