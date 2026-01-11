using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace FamilyTreeSystem.Migrations
{
    /// <inheritdoc />
    public partial class UpdateZiweiColumnNames : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "Character",
                table: "Ziwei");

            migrationBuilder.RenameColumn(
                name: "Id",
                table: "Ziwei",
                newName: "ZiweiId");

            migrationBuilder.AddColumn<string>(
                name: "ZiweiChar",
                table: "Ziwei",
                type: "TEXT",
                maxLength: 2,
                nullable: false,
                defaultValue: "");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "ZiweiChar",
                table: "Ziwei");

            migrationBuilder.RenameColumn(
                name: "ZiweiId",
                table: "Ziwei",
                newName: "Id");

            migrationBuilder.AddColumn<string>(
                name: "Character",
                table: "Ziwei",
                type: "TEXT",
                maxLength: 10,
                nullable: false,
                defaultValue: "");
        }
    }
}
