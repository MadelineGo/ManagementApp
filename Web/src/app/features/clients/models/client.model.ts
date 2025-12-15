export interface Client {
    id?: number;
    name: string;
    last_name?: string; // Optional as API might not return it
    email: string;
    phone_number?: string;
    address?: string;
    isActive?: boolean;
}

export interface ClientResponse {
    data: Client[];
    // Add pagination fields if API returns them
}
