export interface AuthUser {
  id: number;
  name: string;
  email: string;
}

export interface LoginResponse {
  token: string;
  user: AuthUser;
}

export interface ShortLink {
  id: number;
  slug: string;
  destination_url: string;
  title: string | null;
  is_active: boolean;
  clicks_count: number;
  short_url: string;
  created_at: string;
  updated_at: string;
}

export interface LinkStatistics {
  total: number;
  last_7_days: number;
  by_day: Array<{ date: string; count: number }>;
  recent_clicks: Array<{
    id: number;
    clicked_at: string;
    ip: string | null;
    user_agent: string | null;
    referer: string | null;
  }>;
}
