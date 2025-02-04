

<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="http://a320afs2ios.wpweb.fr/mcdu/">
    <img src="./logo.png" alt="Logo" width="120" height="120">
  </a>

<h3 align="center">Netyparneo by E926Neo</h3>

  <p align="center">
    An application allowing university to manage their contracts with enterprises
  </p>
</div>

[![MIT License][license-shield]][license-url]
![php-shield]
![python-shield]
![postgre-shield]
![bootstrap-shield]
![tailwind-shield]
![phpstorm-shield]

<!-- ABOUT THE PROJECT -->
## About The Project

![Product Name Screen Shot][product-screenshot]

This application provides a user-friendly and easy-to-use interface to manage all aspects of the contractual relationship between the university and its partner companies.

<!-- GETTING STARTED -->
## Getting Started

### Prerequisites

Starts by making sure you have docker and docker-compose installed on your environment.
* Install on Ubuntu
  ```sh
  sudo apt-get install docker docker-compose
  sudo usermod -aG <user> docker
  reboot
  ```

### Installation

_This part will help you to generate fake data in order to populate the database, you need a little bit of knowledge in Python in order to tune the amount of data._

1. Clone the repository
2. Build Docker images
    ```sh
    docker-compose build
    ```
3. Run the application
    ```sh
    docker-compose up -d
    ```
4. Go to http://localhost:8888
5. Enjoy !
6. (Optional) Fill your database
   1. Install Python3 and PIP (or just import mock.sql to your database, and you're done)
   2. Go to db/mocks/
   3. Install dependencies
   ```sh
   pip install -r requirements.txt
   ```
   4. Edit constants to fit your needs in mock.py
   5. Run mock.py
   ```sh
   python mock.py
   ```
   6. Import .csv files inside csv folder into your database with third-party apps such as DataGrip

<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE.txt` for more information.

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/othneildrew/Best-README-Template.svg?style=for-the-badge
[contributors-url]: https://github.com/othneildrew/Best-README-Template/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/othneildrew/Best-README-Template.svg?style=for-the-badge
[forks-url]: https://github.com/othneildrew/Best-README-Template/network/members
[stars-shield]: https://img.shields.io/github/stars/othneildrew/Best-README-Template.svg?style=for-the-badge
[stars-url]: https://github.com/othneildrew/Best-README-Template/stargazers
[issues-shield]: https://img.shields.io/github/issues/othneildrew/Best-README-Template.svg?style=for-the-badge
[issues-url]: https://github.com/othneildrew/Best-README-Template/issues
[license-shield]: https://img.shields.io/github/license/othneildrew/Best-README-Template.svg?style=for-the-badge
[license-url]: https://github.com/othneildrew/Best-README-Template/blob/master/LICENSE.txt
[product-screenshot]: ./Screenshot.png
[php-shield]: https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white
[tailwind-shield]: https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white
[bootstrap-shield]: https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white
[postgre-shield]: https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql&logoColor=white
[python-shield]: https://img.shields.io/badge/Python-14354C?style=for-the-badge&logo=python&logoColor=white
[phpstorm-shield]: http://img.shields.io/badge/-PHPStorm-181717?style=for-the-badge&logo=phpstorm&logoColor=white