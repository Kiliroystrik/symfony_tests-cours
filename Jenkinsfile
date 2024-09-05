pipeline {
    agent {
        docker {
            image 'symfony_base-php'
            // Utiliser le chemin Linux et forcer le répertoire de travail dans Docker
            args '-v /c/ProgramData/Jenkins:/workspace -w /workspace --entrypoint=""'
            reuseNode true
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
