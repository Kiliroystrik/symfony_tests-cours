pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                // Correction de la syntaxe Git
                git branch: 'main', url: 'https://github.com/Kiliroystrik/symfony_tests-cours.git'
            }
        }
        
        stage('Install'){
            steps{
                script{
                    // Install Composer dependencies
                    // Utiliser bat si sur Windows, sinon utiliser sh sur Linux
                    bat 'composer install --no-interaction --prefer-dist'
                }
            }
        }
        
        stage('Tests'){
            steps{
                script{
                    // Correction du chemin de PHPUnit (manque un slash avant vendor)
                    bat '.\\vendor\\bin\\phpunit'
                }
            }
        }
    }
}
