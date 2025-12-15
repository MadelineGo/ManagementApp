export interface LoginRequest {
    email: string;
    password: string;
}

export interface RegisterRequest {
    name: string;
    lastName: string;
    email: string;
    password: string;
    confirmPassword?: string;
    phoneNumber?: string;
}

export interface AuthResponse {
    token: string;
    expiration: string;
}

export interface User {
    userId: number;
    name: string;      // Was firstName
    lastname: string;  // Was lastName
    username: string;
    email: string;
    roles?: string[];  // Optional as it wasn't in the JSON
}
