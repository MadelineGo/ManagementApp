import { Injectable, inject, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap, catchError, of, map } from 'rxjs';
import { Router } from '@angular/router';
import { LoginRequest, RegisterRequest, AuthResponse, User } from './auth.models';

@Injectable({
    providedIn: 'root'
})
export class AuthService {
    private http = inject(HttpClient);
    private router = inject(Router);
    private apiUrl = '/api/auth';
    private tokenKey = 'auth_token';
    private userKey = 'auth_user';

    // Signals for state management
    currentUser = signal<User | null>(null);
    isLoading = signal<boolean>(false);

    constructor() {
        this.loadToken();
    }

    private loadToken() {
        const token = localStorage.getItem(this.tokenKey);
        if (token) {
            // Try to hydrate user from cached info for immediate display
            const cachedUser = localStorage.getItem(this.userKey);
            if (cachedUser) {
                try {
                    this.currentUser.set(JSON.parse(cachedUser));
                } catch {
                    this.currentUser.set(null);
                }
            }

            // Also attempt to refresh user info from backend
            this.getUserInfo().subscribe();
        }
    }

    login(credentials: LoginRequest): Observable<AuthResponse> {
        this.isLoading.set(true);
        return this.http.post<AuthResponse>(`${this.apiUrl}/login`, credentials).pipe(
            tap(response => {
                this.setSession(response);
                this.getUserInfo().subscribe();
                this.isLoading.set(false);
                this.router.navigate(['/dashboard']);
            }),
            catchError(err => {
                this.isLoading.set(false);
                throw err;
            })
        );
    }

    register(data: RegisterRequest): Observable<AuthResponse> {
        this.isLoading.set(true);
        return this.http.post<AuthResponse>(`${this.apiUrl}/register`, data).pipe(
            tap(response => {
                this.setSession(response);
                this.getUserInfo().subscribe();
                this.isLoading.set(false);
                this.router.navigate(['/dashboard']);
            }),
            catchError(err => {
                this.isLoading.set(false);
                throw err;
            })
        );
    }

    getUserInfo(): Observable<User> {
        return this.http.get<User>(`${this.apiUrl}/info`).pipe(
            tap(user => {
                this.currentUser.set(user);
                // Cache user so we can restore it on page refresh
                localStorage.setItem(this.userKey, JSON.stringify(user));
            }),
            catchError(err => {
                if (err.status === 401 || err.status === 403) {
                    this.logout();
                }
                // On other errors, keep any cached user so the UI
                // can still show the last known username.
                return of(null as any);
            })
        );
    }

    logout() {
        localStorage.removeItem(this.tokenKey);
        localStorage.removeItem(this.userKey);
        this.currentUser.set(null);
        this.router.navigate(['/auth/login']);
    }

    private setSession(authResult: AuthResponse) {
        localStorage.setItem(this.tokenKey, authResult.token);
    }

    getToken(): string | null {
        return localStorage.getItem(this.tokenKey);
    }
}
