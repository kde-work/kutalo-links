import { Component, computed, inject } from '@angular/core';
import { httpResource } from '@angular/common/http';
import { Router, RouterLink } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatChipsModule } from '@angular/material/chips';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatTableModule } from '@angular/material/table';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatTooltipModule } from '@angular/material/tooltip';
import { AuthService } from '../../core/auth/auth.service';
import { LinksApiService } from './links-api.service';
import { ShortLink } from './models';

@Component({
  selector: 'app-links-list',
  imports: [
    RouterLink,
    MatToolbarModule,
    MatButtonModule,
    MatCardModule,
    MatTableModule,
    MatIconModule,
    MatChipsModule,
    MatSnackBarModule,
    MatProgressSpinnerModule,
    MatTooltipModule,
  ],
  template: `
    <mat-toolbar color="primary">
      <span>Kutalo Links</span>
      <span class="spacer"></span>
      <span class="user">{{ auth.user()?.email }}</span>
      <button mat-icon-button (click)="auth.logout()" matTooltip="Выйти">
        <mat-icon>logout</mat-icon>
      </button>
    </mat-toolbar>

    <div class="page">
      <div class="header">
        <h1>Короткие ссылки</h1>
        <button mat-flat-button color="primary" routerLink="/links/new">
          <mat-icon>add</mat-icon>
          Создать
        </button>
      </div>

      @if (linksResource.isLoading()) {
        <div class="center"><mat-spinner /></div>
      } @else if (linksResource.error()) {
        <mat-card><mat-card-content>Не удалось загрузить ссылки</mat-card-content></mat-card>
      } @else {
        <mat-card>
          <table mat-table [dataSource]="links()" class="full-width">
            <ng-container matColumnDef="slug">
              <th mat-header-cell *matHeaderCellDef>Slug</th>
              <td mat-cell *matCellDef="let link">
                <a [href]="link.short_url" target="_blank" rel="noopener">{{ link.slug }}</a>
              </td>
            </ng-container>
            <ng-container matColumnDef="destination">
              <th mat-header-cell *matHeaderCellDef>Назначение</th>
              <td mat-cell *matCellDef="let link" class="destination">{{ link.destination_url }}</td>
            </ng-container>
            <ng-container matColumnDef="clicks">
              <th mat-header-cell *matHeaderCellDef>Переходы</th>
              <td mat-cell *matCellDef="let link">{{ link.clicks_count }}</td>
            </ng-container>
            <ng-container matColumnDef="status">
              <th mat-header-cell *matHeaderCellDef>Статус</th>
              <td mat-cell *matCellDef="let link">
                <mat-chip [class.active]="link.is_active">{{ link.is_active ? 'Активна' : 'Выключена' }}</mat-chip>
              </td>
            </ng-container>
            <ng-container matColumnDef="actions">
              <th mat-header-cell *matHeaderCellDef></th>
              <td mat-cell *matCellDef="let link">
                <button mat-icon-button [routerLink]="['/links', link.id, 'stats']" matTooltip="Статистика">
                  <mat-icon>bar_chart</mat-icon>
                </button>
                <button mat-icon-button [routerLink]="['/links', link.id, 'edit']" matTooltip="Редактировать">
                  <mat-icon>edit</mat-icon>
                </button>
                <button mat-icon-button color="warn" (click)="remove(link)" matTooltip="Удалить">
                  <mat-icon>delete</mat-icon>
                </button>
              </td>
            </ng-container>
            <tr mat-header-row *matHeaderRowDef="columns"></tr>
            <tr mat-row *matRowDef="let row; columns: columns"></tr>
          </table>
        </mat-card>
      }
    </div>
  `,
  styles: `
    .spacer { flex: 1; }
    .user { font-size: 14px; margin-right: 8px; }
    .page { padding: 24px; max-width: 1200px; margin: 0 auto; }
    .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .full-width { width: 100%; }
    .destination { max-width: 360px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .center { display: flex; justify-content: center; padding: 48px; }
    mat-chip.active { background: #e8f5e9; }
  `,
})
export class LinksListComponent {
  readonly auth = inject(AuthService);
  private readonly api = inject(LinksApiService);
  private readonly snackBar = inject(MatSnackBar);

  readonly columns = ['slug', 'destination', 'clicks', 'status', 'actions'];

  readonly linksResource = httpResource<ShortLink[]>(() => '/api/links');

  readonly links = computed(() => this.linksResource.value() ?? []);

  remove(link: ShortLink): void {
    if (!confirm(`Удалить ссылку ${link.slug}?`)) {
      return;
    }

    this.api.delete(link.id).subscribe({
      next: () => {
        this.snackBar.open('Ссылка удалена', 'OK', { duration: 3000 });
        this.linksResource.reload();
      },
      error: () => this.snackBar.open('Ошибка удаления', 'OK', { duration: 3000 }),
    });
  }
}
