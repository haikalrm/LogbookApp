import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  vus: 1,
  duration: '1m',

  thresholds: {
    http_req_duration: [
      'p(95)<1000',
      'p(99)<1500',
    ],
    http_req_failed: [
      'rate<0.05',
    ],
  },
};

const BASE_URL = 'http://localhost:8000';

export default function () {
  const payload = JSON.stringify({
    login: 'admin@example.com',
    password: 'password123',
  });

  const params = {
    headers: {
      'Content-Type': 'application/json',
    },
  };

  const res = http.post(`${BASE_URL}/api/v1/login`, payload, params);

  check(res, {
    'status is 200': (r) => r.status === 200,
  });

  sleep(1);
}
