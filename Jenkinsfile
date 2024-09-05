pipeline {
    agent {
        docker {
            image 'symfony_base-php'
            // Désactiver les arguments de volume et de répertoire de travail
            args '--entrypoint=""'
        }
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/Kiliroystrik/symfony_tests-cours.git'
            }
        }

        stage('Install') {
            steps {
                script {
                    // Installer les dépendances Composer
                    sh 'composer install --no-interaction --prefer-dist'
                }
            }
        }

        stage('Tests') {
            steps {
                script {
                    // Lancer PHPUnit
                    sh './vendor/bin/phpunit'
                }
            }
        }
    }
}
