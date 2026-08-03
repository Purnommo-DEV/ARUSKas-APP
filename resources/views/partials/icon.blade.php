@switch($name)
    @case('dashboard')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 13h6V4H4v9Zm0 7h6v-4H4v4Zm10 0h6v-9h-6v9Zm0-16v4h6V4h-6Z" stroke-linejoin="round"/></svg>
        @break
    @case('transaction')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 7h11m0 0-3-3m3 3-3 3M17 17H6m0 0 3 3m-3-3 3-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
    @case('category')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h4A1.5 1.5 0 0 1 11 5.5v4A1.5 1.5 0 0 1 9.5 11h-4A1.5 1.5 0 0 1 4 9.5v-4Zm9 0A1.5 1.5 0 0 1 14.5 4h4A1.5 1.5 0 0 1 20 5.5v4a1.5 1.5 0 0 1-1.5 1.5h-4A1.5 1.5 0 0 1 13 9.5v-4Zm-9 9A1.5 1.5 0 0 1 5.5 13h4a1.5 1.5 0 0 1 1.5 1.5v4A1.5 1.5 0 0 1 9.5 20h-4A1.5 1.5 0 0 1 4 18.5v-4Zm9 0a1.5 1.5 0 0 1 1.5-1.5h4a1.5 1.5 0 0 1 1.5 1.5v4a1.5 1.5 0 0 1-1.5 1.5h-4a1.5 1.5 0 0 1-1.5-1.5v-4Z"/></svg>
        @break
    @case('report')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 3.5h8.2L19 7.3V20a1.5 1.5 0 0 1-1.5 1.5h-10A1.5 1.5 0 0 1 6 20V5a1.5 1.5 0 0 1 1-1.5Z" stroke-linejoin="round"/><path d="M15 3.5V8h4M9 12h6M9 16h6" stroke-linecap="round"/></svg>
        @break
    @case('profile')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0" stroke-linecap="round"/></svg>
        @break
    @case('donation')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 20.5S4.5 16 4.5 9.8A3.8 3.8 0 0 1 12 8.3a3.8 3.8 0 0 1 7.5 1.5c0 6.2-7.5 10.7-7.5 10.7Z" stroke-linejoin="round"/></svg>
        @break
    @case('users')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M16 20v-1.5A3.5 3.5 0 0 0 12.5 15h-5A3.5 3.5 0 0 0 4 18.5V20m15.5 0v-1a3 3 0 0 0-2.5-2.96M10 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm5.5-6.88a3.5 3.5 0 0 1 0 6.76" stroke-linecap="round"/></svg>
        @break
    @case('settings')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M9.8 4.1 10.5 2h3l.7 2.1a8.3 8.3 0 0 1 1.6.9l2.1-.5 1.5 2.6-1.5 1.6c.1.5.1 1.1.1 1.6l1.5 1.6-1.5 2.6-2.1-.5a8.3 8.3 0 0 1-1.6.9l-.7 2.1h-3l-.7-2.1a8.3 8.3 0 0 1-1.6-.9l-2.1.5-1.5-2.6 1.5-1.6a8.6 8.6 0 0 1 0-1.6L4.7 7.1l1.5-2.6 2.1.5a8.3 8.3 0 0 1 1.5-.9Z"/><circle cx="12" cy="10.3" r="2.7"/></svg>
        @break
    @case('logout')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 5H5v14h5m4-3 4-4-4-4m4 4H9" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
    @default
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8"/></svg>
@endswitch
