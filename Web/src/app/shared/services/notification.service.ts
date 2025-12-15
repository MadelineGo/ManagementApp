import { Injectable, inject } from '@angular/core';
import { MatSnackBar, MatSnackBarConfig } from '@angular/material/snack-bar';

@Injectable({
    providedIn: 'root'
})
export class NotificationService {
    private snackBar = inject(MatSnackBar);

    private defaultConfig: MatSnackBarConfig = {
        duration: 4000,
        horizontalPosition: 'end',
        verticalPosition: 'top',
    };

    /**
     * Show a success message
     */
    success(message: string, duration?: number) {
        this.snackBar.open(message, '✓', {
            ...this.defaultConfig,
            duration: duration || this.defaultConfig.duration,
            panelClass: ['snackbar-success']
        });
    }

    /**
     * Show an error message
     */
    error(message: string, duration?: number) {
        this.snackBar.open(message, '✕', {
            ...this.defaultConfig,
            duration: duration || this.defaultConfig.duration,
            panelClass: ['snackbar-error']
        });
    }

    /**
     * Show a warning message
     */
    warning(message: string, duration?: number) {
        this.snackBar.open(message, '⚠', {
            ...this.defaultConfig,
            duration: duration || this.defaultConfig.duration,
            panelClass: ['snackbar-warning']
        });
    }

    /**
     * Show an info message
     */
    info(message: string, duration?: number) {
        this.snackBar.open(message, 'ℹ', {
            ...this.defaultConfig,
            duration: duration || this.defaultConfig.duration,
            panelClass: ['snackbar-info']
        });
    }

    /**
     * Show a confirmation dialog with action
     */
    confirm(message: string, action: string = 'Confirm'): Promise<boolean> {
        return new Promise((resolve) => {
            const snackBarRef = this.snackBar.open(message, action, {
                duration: 10000,
                horizontalPosition: 'center',
                verticalPosition: 'top',
                panelClass: ['snackbar-confirm']
            });

            snackBarRef.onAction().subscribe(() => {
                resolve(true);
            });

            snackBarRef.afterDismissed().subscribe((info) => {
                if (!info.dismissedByAction) {
                    resolve(false);
                }
            });
        });
    }
}
