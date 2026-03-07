import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, catchError, map, throwError } from 'rxjs';
import { OpenPmuApiResponse, RaceResult, RaceSearchFilters } from '../../models/dashboard.models';

@Injectable({ providedIn: 'root' })
export class ApiService {
  private readonly baseUrl = '/api';

  constructor(private http: HttpClient) {}

  searchRaceResults(filters: RaceSearchFilters = {}): Observable<RaceResult[]> {
    const params = this.toParams(filters);

    return this.http
      .get<OpenPmuApiResponse>(`${this.baseUrl}/pmu-results`, { params })
      .pipe(
        map((response) => this.unwrapResults(response)),
        catchError((error) => {
          const message = error?.message ?? 'Impossible de récupérer les données PMU.';
          return throwError(() => new Error(message));
        }),
      );
  }

  private unwrapResults(response: OpenPmuApiResponse): RaceResult[] {
    if (!response || response.error) {
      return [];
    }

    return Array.isArray(response.message) ? response.message : [];
  }

  private toParams(filters: RaceSearchFilters): HttpParams {
    let params = new HttpParams();

    Object.entries(filters).forEach(([key, value]) => {
      const cleanValue = value?.trim();
      if (cleanValue) {
        params = params.set(key, cleanValue);
      }
    });

    return params;
  }
}
