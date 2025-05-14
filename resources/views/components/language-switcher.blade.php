<div class="language-switcher">
    <div class="dropdown">
        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-globe me-1"></i>
            {{ session('locale', 'en') == 'id' ? 'Indonesia' : 'English' }}
        </button>
        <ul class="dropdown-menu" aria-labelledby="languageDropdown">
            <li>
                <a class="dropdown-item {{ session('locale', 'en') == 'en' ? 'active' : '' }}" href="{{ route('language.switch', ['locale' => 'en']) }}">
                    🇺🇸 English
                </a>
            </li>
            <li>
                <a class="dropdown-item {{ session('locale', 'en') == 'id' ? 'active' : '' }}" href="{{ route('language.switch', ['locale' => 'id']) }}">
                    🇮🇩 Indonesia
                </a>
            </li>
        </ul>
    </div>
</div>

<style>
.language-switcher {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
}

.language-switcher .dropdown-menu {
    min-width: 120px;
}

.language-switcher .dropdown-item.active {
    background-color: var(--primary-color);
    color: white;
}

@media (max-width: 768px) {
    .language-switcher {
        top: 10px;
        right: 10px;
    }
}
</style>
</style>
