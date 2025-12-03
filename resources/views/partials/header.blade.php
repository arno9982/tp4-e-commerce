 <header>
        <div class="promo-box">
            <div class="container">
                <div class="promo-first">
                    <span>Black</span>
                    <span>Friday</span>
                </div>
                <div class="promo-second">
                    <span>up to&nbsp;</span>
                    <span>50%</span>
                    <span>&nbsp;OFF</span>
                </div>
                <div class="promo-third">
                    <a href="product.html" class="btn-shop">
                        Shop Now
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor">
                            <path d="m560-240-56-58 142-142H160v-80h486L504-662l56-58 240 240-240 240Z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="header-box">
            <div class="container">
                <div class="header-first">
                   <img src="{{ asset('images/logo.jpg') }}" alt="Eazyshop logo">
                    <span>EAZYSHOP</span>
                </div>
                <div class="header-second">
                    <input type="text" name="search" id="search" placeholder="Search">
                    <button type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="header-third">
                    <button type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor">
                            <path d="M480-360q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35Zm0 80q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480q0 83-58.5 141.5T480-280ZM200-440H40v-80h160v80Zm720 0H760v-80h160v80ZM440-760v-160h80v160h-80Zm0 720v-160h80v160h-80ZM256-650l-101-97 57-59 96 100-52 56Zm492 496-97-101 53-55 101 97-57 59Zm-98-550 97-101 59 57-100 96-56-52ZM154-212l101-97 55 53-97 101-59-57Zm326-268Z"/>
                        </svg>
                    </button>
                    <button type="button" class="btn-cart">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor">
                            <path d="M280-80q-33 0-56.5-23.5T200-160q0-33 23.5-56.5T280-240q33 0 56.5 23.5T360-160q0 33-23.5 56.5T280-80Zm400 0q-33 0-56.5-23.5T600-160q0-33 23.5-56.5T680-240q33 0 56.5 23.5T760-160q0 33-23.5 56.5T680-80ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z"/>
                        </svg>
                        <span class="nb-cart"></span>
                    </button>
                    <button type="button">
                        <a href="{{ route('profile.edit') }}" class="account-btn">
                                <img src="{{ asset('images/user.jpeg') }}" alt="Account">
                        </a>
                    </button>
                </div>
            </div>
        </div>
        <div class="menu-box">
            <div class="container">
                <div class="menu-first">
                    <nav>
                        <a href="{{ route('home') }}">Home</a>
                        <a href="{{ route('products.index') }}" class="active-link">Products</a>
                        <a href="{{ route('contact') }}">Contact</a>
                        <a href="{{ route('about') }}">About us</a>
                    @auth
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    @endauth
                        <a href="{{ route('terms') }}">Terms & Conditions</a>
                        <a href="{{ route('blog.index') }}">Blog</a>
                    </nav>

                </div>
                <div class="menu-second">
                    <span><i class="fas fa-globe"></i></span>
                    <select name="lang" id="lang">
                        <option value="en">ENG</option>
                        <option value="fr">FRA</option>
                        <option value="es">ESP</option>
                    </select>
                    <span>&nbsp;</span>
                    <span><i class="fas fa-yen-sign"></i></span>
                    <select name="currency" id="currency">
                        <option value="usd">USD</option>
                        <option value="eur">EUR</option>
                        <option value="xaf">XAF</option>
                    </select>
                </div>
            </div>
        </div>
    </header>

