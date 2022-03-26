<div id="top"></div>

<!-- PROJECT SHIELDS -->
[![GPLv3 License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]


<!-- PROJECT LOGO -->
<br />
<div align="center">

<h3 align="center">Moodle LMS report plugin for course module statistics</h3>

  <p align="center">
    This report plugin shows number of activity and resource instances per course in downloadable Excel file. 
</div>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->

## About The Project

Report plugin coursemodstats allows you to download Excel file report with module instance counts grouped by module type and course name. It also renders root course category and
course current category for easier navigation.

#### Excel file example

![Moodle coursemodstats excel example][product-screenshot-excel]

#### Example of report form.

![Moodle coursemodstats form][product-screenshot-form]

The plugin entry point link could be found in Moodle site administration block.
![Moodle coursemodstats admin link][product-screenshot-menu]

### Supported modules

All visible modules from mdl_modules database table are included automatically and their names are translated to your current Moodle language.

### Form options

* Count only visible modules - This option will ignore any hidden module on a system level and there fore not count it's course instances.

* Filter only visible courses - Only visible courses will be included in report.

* Select all available courses - When this option is checked all courses will be included in report. Otherwise you can select specific courses from the list. Previous filter will
  be applied to this selection regardless.

<p align="right">(<a href="#top">back to top</a>)</p>

### Built With

* [PHP](https://www.php.net/)
* [Moodle](https://www.moodle.org/)

<!-- GETTING STARTED -->

## Getting Started

Checkout this project and open it with PHP suported IDE. You may use VSCode or PHPStorm.

### Prerequisites

Please make sure that you are using php version 7.x and as a precaution backup your Moodle installation before installing this report plugin.

This plugin requires 3rd party dependency `PhpOffice\PhpSpreadsheet` that is already bundled with Moodle version +3.9.

### Installation

1. If you choose to manually install this plugin then place git repository contents in your moodle root directory under ./report/coursemodstats subdirectory.
2. If you are installing this plugin from moodle.org through your Moodle administration interface then locate coursemodstats and start the installation process from your browser.

<!-- CONTRIBUTING -->

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement". Don't forget to
give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#top">back to top</a>)</p>



<!-- LICENSE -->

## License

Distributed under the GPL v3 License. See `LICENSE` file for more information.

<p align="right">(<a href="#top">back to top</a>)</p>



<!-- CONTACT -->

## Contact

Project Link: [https://github.com/tezvi/moodle-report_coursemodstats](https://github.com/tezvi/moodle-report_coursemodstats)

<p align="right">(<a href="#top">back to top</a>)</p>


<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[license-shield]: https://img.shields.io/github/license/tezvi/moodle-report_coursemodstats.svg?style=for-the-badge

[license-url]: https://github.com/tezvi/moodle-report_coursemodstats/blob/master/LICENSE

[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555

[linkedin-url]: https://www.linkedin.com/in/andrej-v-11481925/

[product-screenshot-menu]: docs/admin-block-report-menu-item.png

[product-screenshot-form]: docs/report_form.png

[product-screenshot-excel]: docs/report_excel_example.png
