import { Component, computed, inject, signal } from '@angular/core';
import { httpResource } from '@angular/common/http';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { DatePipe } from '@angular/common';
import { BaseChartDirective } from 'ng2-charts';
import { ChartConfiguration } from 'chart.js';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatTableModule } from '@angular/material/table';
import { MatToolbarModule } from '@angular/material/toolbar';
import { LinkStatistics } from './models';

@Component({
  selector: 'app-link-stats',
  imports: [
    RouterLink,
    DatePipe,
    MatToolbarModule,
    MatButtonModule,
    MatCardModule,
    MatTableModule,
    MatProgressSpinnerModule,
    BaseChartDirective,
  ],
  template: `
    <mat-toolbar>
      <a mat-button routerLink="/">← Назад</a>
      <span>Статистика ссылки #{{ linkId() }}</span>
    </mat-toolbar>

    <div class="page">
      @if (statsResource.isLoading()) {
        <div class="center"><mat-spinner /></div>
      } @else if (statsResource.error()) {
        <mat-card><mat-card-content>Не удалось загрузить статистику</mat-card-content></mat-card>
      } @else {
        <div class="cards">
          <mat-card><mat-card-content><div class="metric">{{ stats()?.total ?? 0 }}</div><div>Всего</div></mat-card-content></mat-card>
          <mat-card><mat-card-content><div class="metric">{{ stats()?.last_7_days ?? 0 }}</div><div>За 7 дней</div></mat-card-content></mat-card>
        </div>

        <mat-card class="chart-card">
          <mat-card-header><mat-card-title>Переходы по дням</mat-card-title></mat-card-header>
          <mat-card-content>
            <canvas baseChart [data]="chartData()" [options]="chartOptions" type="line"></canvas>
          </mat-card-content>
        </mat-card>

        <mat-card>
          <mat-card-header><mat-card-title>Последние переходы</mat-card-title></mat-card-header>
          <table mat-table [dataSource]="stats()?.recent_clicks ?? []" class="full-width">
            <ng-container matColumnDef="clicked_at">
              <th mat-header-cell *matHeaderCellDef>Время</th>
              <td mat-cell *matCellDef="let row">{{ row.clicked_at | date: 'short' }}</td>
            </ng-container>
            <ng-container matColumnDef="ip">
              <th mat-header-cell *matHeaderCellDef>IP</th>
              <td mat-cell *matCellDef="let row">{{ row.ip || '—' }}</td>
            </ng-container>
            <ng-container matColumnDef="referer">
              <th mat-header-cell *matHeaderCellDef>Referer</th>
              <td mat-cell *matCellDef="let row" class="referer">{{ row.referer || '—' }}</td>
            </ng-container>
            <tr mat-header-row *matHeaderRowDef="columns"></tr>
            <tr mat-row *matRowDef="let row; columns: columns"></tr>
          </table>
        </mat-card>
      }
    </div>
  `,
  styles: `
    .page { padding: 24px; max-width: 1000px; margin: 0 auto; display: grid; gap: 16px; }
    .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; }
    .metric { font-size: 32px; font-weight: 600; }
    .chart-card canvas { max-height: 320px; }
    .full-width { width: 100%; }
    .referer { max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .center { display: flex; justify-content: center; padding: 48px; }
  `,
})
export class LinkStatsComponent {
  private readonly route = inject(ActivatedRoute);

  readonly columns = ['clicked_at', 'ip', 'referer'];
  readonly linkId = signal(Number(this.route.snapshot.paramMap.get('id')));

  readonly statsResource = httpResource<LinkStatistics>(
    () => `/api/links/${this.linkId()}/statistics?days=30`,
  );

  readonly stats = computed(() => this.statsResource.value());

  readonly chartData = computed<ChartConfiguration<'line'>['data']>(() => {
    const byDay = this.stats()?.by_day ?? [];
    return {
      labels: byDay.map((d) => d.date),
      datasets: [
        {
          data: byDay.map((d) => d.count),
          label: 'Переходы',
          fill: false,
          tension: 0.3,
          borderColor: '#1976d2',
        },
      ],
    };
  });

  readonly chartOptions: ChartConfiguration<'line'>['options'] = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: { beginAtZero: true, ticks: { precision: 0 } },
    },
  };
}
