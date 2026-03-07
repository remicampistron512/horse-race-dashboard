export interface RaceSearchFilters {
  date?: string;
  prix?: string;
  hippo?: string;
}

export interface OpenPmuApiResponse {
  error: boolean;
  message: RaceResult[];
}

export interface RaceResult {
  date: string;
  type: string;
  montant: string;
  distance: string;
  pix: string;
  lieu: string;
  'r/c': string;
  partants: string;
  non_partants: string;
  arrivee: string;
  details: string;
}
