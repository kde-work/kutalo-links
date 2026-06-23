import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { LinkStatistics, ShortLink } from './models';

@Injectable({ providedIn: 'root' })
export class LinksApiService {
  constructor(private readonly http: HttpClient) {}

  create(payload: {
    slug?: string;
    destination_url: string;
    title?: string;
  }): Observable<ShortLink> {
    return this.http.post<ShortLink>('/api/links', payload);
  }

  update(id: number, payload: { title?: string; is_active?: boolean }): Observable<ShortLink> {
    return this.http.patch<ShortLink>(`/api/links/${id}`, payload);
  }

  updateDestination(id: number, destination_url: string): Observable<ShortLink> {
    return this.http.patch<ShortLink>(`/api/links/${id}/destination`, { destination_url });
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`/api/links/${id}`);
  }

  getStatistics(id: number, days = 30): Observable<LinkStatistics> {
    return this.http.get<LinkStatistics>(`/api/links/${id}/statistics`, {
      params: { days: String(days) },
    });
  }
}
