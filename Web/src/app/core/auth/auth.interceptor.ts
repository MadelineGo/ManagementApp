import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { AuthService } from './auth.service';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
    const authService = inject(AuthService);
    console.log('Interceptor: Handling request to', req.url); // Debug log

    // Skip adding token for public endpoints
    if (req.url.includes('/login') || req.url.includes('/register')) {
        return next(req);
    }

    const token = authService.getToken();

    if (token) {
        console.log('Interceptor: Attaching token', token.substring(0, 10) + '...');
        const cloned = req.clone({
            setHeaders: {
                Authorization: `Bearer ${token}`
            }
        });
        return next(cloned);
    } else {
        console.warn('Interceptor: No token found in AuthService');
    }

    return next(req);
};
