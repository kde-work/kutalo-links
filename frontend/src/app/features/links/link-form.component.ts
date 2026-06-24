import { Component, inject, OnInit, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatToolbarModule } from '@angular/material/toolbar';
import { LinksApiService } from './links-api.service';
import { ShortLink } from './models';

@Component({
  selector: 'app-link-form',
  imports: [
    ReactiveFormsModule,
    RouterLink,
    MatToolbarModule,
    MatButtonModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatCheckboxModule,
    MatSnackBarModule,
  ],
  template: `
    <mat-toolbar>
      <a mat-button routerLink="/">← Назад</a>
      <span>{{ isEdit() ? 'Редактирование' : 'Новая ссылка' }}</span>
    </mat-toolbar>

    <div class="page">
      <mat-card>
        <mat-card-content>
          <form [formGroup]="form" (ngSubmit)="submit()">
            @if (!isEdit()) {
              <mat-form-field appearance="outline" class="full-width">
                <mat-label>Slug (необязательно)</mat-label>
                <input matInput formControlName="slug" placeholder="my-link" />
                <mat-hint>1-32 символа. Если пусто — сгенерируется автоматически</mat-hint>
              </mat-form-field>
            }

            <mat-form-field appearance="outline" class="full-width">
              <mat-label>URL назначения</mat-label>
              <input matInput formControlName="destination_url" placeholder="https://example.com" />
            </mat-form-field>

            <mat-form-field appearance="outline" class="full-width">
              <mat-label>Название</mat-label>
              <input matInput formControlName="title" />
            </mat-form-field>

            @if (isEdit()) {
              <mat-checkbox formControlName="is_active">Ссылка активна</mat-checkbox>
            }

            <div class="actions">
              <button mat-flat-button color="primary" type="submit" [disabled]="loading() || form.invalid">
                Сохранить
              </button>
            </div>
          </form>
        </mat-card-content>
      </mat-card>
    </div>
  `,
  styles: `
    .page { padding: 24px; max-width: 720px; margin: 0 auto; }
    .full-width { width: 100%; display: block; margin-bottom: 12px; }
    .actions { margin-top: 16px; }
  `,
})
export class LinkFormComponent implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly api = inject(LinksApiService);
  private readonly http = inject(HttpClient);
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly snackBar = inject(MatSnackBar);

  readonly loading = signal(false);
  readonly isEdit = signal(false);
  private linkId: number | null = null;

  readonly form = this.fb.nonNullable.group({
    slug: [''],
    destination_url: ['', [Validators.required]],
    title: [''],
    is_active: [true],
  });

  ngOnInit(): void {
    const idParam = this.route.snapshot.paramMap.get('id');
    if (idParam) {
      this.isEdit.set(true);
      this.linkId = Number(idParam);
      this.form.controls.slug.disable();
      this.http.get<ShortLink>(`/api/links/${this.linkId}`).subscribe({
        next: (link) => {
          this.form.patchValue({
            destination_url: link.destination_url,
            title: link.title ?? '',
            is_active: link.is_active,
          });
        },
      });
    }
  }

  submit(): void {
    if (this.form.invalid) {
      return;
    }

    this.loading.set(true);
    const raw = this.form.getRawValue();

    if (this.isEdit() && this.linkId !== null) {
      this.api.updateDestination(this.linkId, raw.destination_url).subscribe({
        next: () =>
          this.api.update(this.linkId!, { title: raw.title || undefined, is_active: raw.is_active }).subscribe({
            next: () => this.onSuccess(),
            error: () => this.onError(),
          }),
        error: () => this.onError(),
      });
      return;
    }

    this.api
      .create({
        slug: raw.slug || undefined,
        destination_url: raw.destination_url,
        title: raw.title || undefined,
      })
      .subscribe({
        next: () => this.onSuccess(),
        error: (err) => {
          this.snackBar.open(err.error?.message ?? 'Ошибка сохранения', 'OK', { duration: 4000 });
          this.loading.set(false);
        },
      });
  }

  private onSuccess(): void {
    this.snackBar.open('Сохранено', 'OK', { duration: 3000 });
    this.router.navigate(['/']);
  }

  private onError(): void {
    this.snackBar.open('Ошибка сохранения', 'OK', { duration: 3000 });
    this.loading.set(false);
  }
}
