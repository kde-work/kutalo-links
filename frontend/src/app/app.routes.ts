import { Routes } from '@angular/router';
import { authGuard } from './core/auth/auth.guard';

export const routes: Routes = [
  {
    path: 'login',
    loadComponent: () => import('./features/auth/login.component').then((m) => m.LoginComponent),
  },
  {
    path: '',
    canActivate: [authGuard],
    loadComponent: () => import('./features/links/links-list.component').then((m) => m.LinksListComponent),
  },
  {
    path: 'links/new',
    canActivate: [authGuard],
    loadComponent: () => import('./features/links/link-form.component').then((m) => m.LinkFormComponent),
  },
  {
    path: 'links/:id/edit',
    canActivate: [authGuard],
    loadComponent: () => import('./features/links/link-form.component').then((m) => m.LinkFormComponent),
  },
  {
    path: 'links/:id/stats',
    canActivate: [authGuard],
    loadComponent: () => import('./features/links/link-stats.component').then((m) => m.LinkStatsComponent),
  },
  { path: '**', redirectTo: '' },
];
