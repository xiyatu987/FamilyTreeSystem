using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace FamilyTreeSystem.Migrations
{
    /// <inheritdoc />
    public partial class AddClanSettingsFields : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<DateTime>(
                name: "AncestorBirthDate",
                table: "Clans",
                type: "TEXT",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "AncestorName",
                table: "Clans",
                type: "TEXT",
                maxLength: 50,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "ClanSurname",
                table: "Clans",
                type: "TEXT",
                maxLength: 20,
                nullable: false,
                defaultValue: "");

            migrationBuilder.AddColumn<string>(
                name: "HallName",
                table: "Clans",
                type: "TEXT",
                maxLength: 50,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "OriginalAddress",
                table: "Clans",
                type: "TEXT",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "Region",
                table: "Clans",
                type: "TEXT",
                maxLength: 100,
                nullable: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "AncestorBirthDate",
                table: "Clans");

            migrationBuilder.DropColumn(
                name: "AncestorName",
                table: "Clans");

            migrationBuilder.DropColumn(
                name: "ClanSurname",
                table: "Clans");

            migrationBuilder.DropColumn(
                name: "HallName",
                table: "Clans");

            migrationBuilder.DropColumn(
                name: "OriginalAddress",
                table: "Clans");

            migrationBuilder.DropColumn(
                name: "Region",
                table: "Clans");
        }
    }
}
