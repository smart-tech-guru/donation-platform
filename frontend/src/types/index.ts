export interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'user';
}

export interface Campaign {
  id: number
  title: string
  description: string
  goal_amount: number
  donations_count: number
  donations_sum_amount?: number
  created_at: string
  updated_at: string
}

export interface Donation {
  id: number;
  amount: number;
  created_at: string;
  user: {
    id: number;
    name: string;
    email: string;
  }
}
