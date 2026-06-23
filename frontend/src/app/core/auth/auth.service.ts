import { HttpErrorResponse } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { tap } from 'rxjs/operators';
import { AuthUser, LoginResponse } from '../../features/links/models';

const TOKEN_KEY = 'kutalo_links_token';

@Injectable({ providedIn: 'root' })
export class AuthService {
  readonly user = signal<AuthUser | null>(null);

  constructor(
    private readonly http: HttpClient,
    private readonly router: Router,
  ) {
    const token = this.getToken();
    if (token) {
      this.loadUser().subscribe({
        error: (error: HttpErrorResponse) => {
          if (error.status === 401) {
            this.clearSession();
          }
        },
      });
    }
  }

  getToken(): string | null {
    return localStorage.getItem(TOKEN_KEY);
  }

  isLoggedIn(): boolean {
    return this.getToken() !== null;
  }

  login(email: string, password: string) {
    return this.http.post<LoginResponse>('/api/login', { email, password }).pipe(
      tap((response) => {
        localStorage.setItem(TOKEN_KEY, response.token);
        this.user.set(response.user);
      }),
    );
  }

  loadUser() {
    return this.http.get<AuthUser>('/api/user').pipe(tap((user) => this.user.set(user)));
  }

  logout(): void {
    const token = this.getToken();
    if (token) {
      this.http.post('/api/logout', {}).subscribe({ error: () => undefined });
    }
    this.clearSession();
  }

  clearSession(): void {
    localStorage.removeItem(TOKEN_KEY);
    this.user.set(null);
    this.router.navigate(['/login']);
  }
}
